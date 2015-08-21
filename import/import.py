#!/usr/bin/env python
"""
A script to import initial data from ProEco into Theros
"""

import logging
import argparse
import sys
import re
import hashlib

parser=argparse.ArgumentParser(description=__doc__, formatter_class=argparse.ArgumentDefaultsHelpFormatter)
parser.add_argument("-w","--works", help="the csv file containing the raw works export from ProEco", metavar="CSV_FILE" , default="travaux.csv", dest="worksFile")
parser.add_argument("-s","--subjects", help="the csv file containing the subjects export from ProEco", metavar="CSV_FILE" , default="cours.csv", dest="subjectsFile")
parser.add_argument("--teachers", help="the csv file containing the teachers export from ProEco", metavar="CSV_FILE" , default="profs.csv", dest="teachersFile")
parser.add_argument("-v","--verbose", action="store_const", dest="logging", const=logging.DEBUG, default=logging.INFO, help="show debug logs")
parser.add_argument("--dsn", help="the dsn to use for db operations", action="store", dest="dsn", default="theros_dev")
parser.add_argument("-i", "--insert", help="insert (ignoring duplicates) data into database (see --dsn)", action="store_true", dest="insertData")
parser.add_argument("-y", "--shoolyear", help="specify the current school year", action="store", dest="schoolyear", default="2014-15")
parser.add_argument("-t", "--truncate", help="truncate all tables", action="store_true", dest="truncate")
args=parser.parse_args()
logging.basicConfig(level=args.logging, stream=sys.stdout, format="%(levelname)7s : %(message)s")
logger=logging.getLogger()

if not args.dsn and args.insertData:
    parser.error("missing --dsn specification")

def iterCsv(fname, header=True):
    with open(fname) as fh:
        for line in fh:
            if header:
                header=False
                continue
            line=line.decode("utf8")
            yield map(lambda s:s.strip(), line.split("\t"))

worksFile=args.worksFile
works=[]
classes=set()
students=set()
compositions=[]
class Work:
    def __init__(self, klass, student, desc, line):
        self.klass=klass
        self.student=student
        self.desc=desc
        self.line=line

for i,line in enumerate(iterCsv(worksFile)):
    klass,student, dummy, foo, desc, grp = line
    klass=klass.replace(" ","").upper()
    if not re.search(r"^\d[A-Z]+$", klass):
        raise ValueError, "line %i contains bad class: %s"%(i+1, klass)
    student=student.replace("  "," ")
    classes.add(klass)
    students.add(student)
    compositions.append(Work(klass, student, desc, i+1))
    if desc:
        works.append(compositions[-1])
        logger.debug("keep %s", works[-1])
    else:
        logger.debug("discarded line %s", line)

logger.info("got %i works, %i students, %i classes", len(works), len(students), len(classes))

subjects={}
for i,line in enumerate(iterCsv(args.subjectsFile, False)):
    code=line[2].strip().upper()
    desc=line[5]
    subjects[code]=desc
logger.info("got %i subjects", len(subjects))

teachings=set()
teachers={}
for i,line in enumerate(iterCsv(args.teachersFile)):
    first,last,dob = line[:3]
    subject=line[-1].strip().upper()
    klass=line[-3].replace(" ","").upper()
    name=first+" "+last
    teachers[name]=hashlib.md5(dob).hexdigest()
    localClasses=[ c for c in classes if c.startswith(klass)]
    logger.debug("%s teaches %s in %s (%i match, %s)", name, subject, klass, len(localClasses), subject in subjects)
    if not klass:
        logger.warn("%s teaches %s in no class", name, subject)
    elif subject not in subjects:
        logger.warn("%s teaches %s in %s : unknown subject", name, subject, klass)
    elif len(localClasses) == 0:
        logger.Warn("%s teaches %s in %s : unknown class", name, subject, klass)
    else:
        for c in localClasses:
            teachings.add((name, subject, c))
logger.info("got %i teachers and %i teachings", len(teachers), len(teachings))
teachings=sorted(teachings)

if args.insertData or args.truncate:
    logging.info("connecting to database")
    import pyodbc
    conn=pyodbc.connect(dsn=args.dsn)
    try:
        db=conn.cursor()

        if args.truncate:
            logging.info("truncate tables")
            result=db.execute("SHOW TABLES").fetchall()
            db.execute("set foreign_key_checks=0")
            for row in result:
                db.execute("TRUNCATE %s"%row[0])
            db.execute("set foreign_key_checks=1")

        if args.insertData:
            logging.info("inserting students")
            params=[(s,) for s in sorted(students)]
            db.executemany("INSERT IGNORE INTO student(st_name) VALUES (?)", params)

            logging.info("inserting classes")
            params=[(c,) for c in sorted(classes)]
            db.executemany("INSERT IGNORE INTO class(cl_desc) VALUES (?)", params)

            logging.info("inserting classes compositions")
            db.execute("INSERT IGNORE INTO schoolyear(sy_desc) VALUES (?)", (args.schoolyear,))
            schoolyear = db.execute("SELECT sy_id FROM schoolyear WHERE sy_desc = ?", (args.schoolyear,)).fetchone().sy_id
            classes=db.execute("SELECT * FROM class").fetchall()
            classes=dict([(c.cl_desc, c.cl_id) for c in classes])
            students=db.execute("SELECT * FROM student").fetchall()
            students=dict([(s.st_name, s.st_id) for s in students])
            db.executemany("INSERT IGNORE INTO student_class(sc_st_id, sc_cl_id, sc_sy_id) VALUES (?,?,?)",[
                (students[c.student], classes[c.klass], schoolyear) for c in compositions
            ])

            logging.info("inserting works")
            query = "INSERT IGNORE INTO raw_data(rd_st_id, rd_cl_id, rd_desc) VALUES (?,?,?)"
            params=[]
            for w in works:
                if w.klass not in classes:
                    logger.error("no such class: %s for work at line %i",w.klass, w.line)
                    continue

                if w.student not in students:
                    logger.error("no such student: %s for work at line %i",w.student, w.line)
                    continue

                classId = classes[w.klass]
                studentId = students[w.student]
                params.append((studentId, classId, w.desc))
            if len(params):
                db.executemany(query, params)
            else:
                logger.warn("no work inserted")

            logging.info("inserting subjects")
            params=sorted(subjects.items())
            db.executemany("INSERT IGNORE INTO subject(sub_code, sub_desc) VALUES (?,?)", params)

            logging.info("inserting teachers")
            params=sorted(teachers.items())
            db.executemany("INSERT IGNORE INTO teacher(tea_fullname, tea_password) VALUES (?,?)", params)

            logging.info("inserting teachings")
            subjects=dict([ (r.sub_code, r.sub_id) for r in db.execute("SELECT * FROM subject").fetchall() ])
            teachers=dict([ (r.tea_fullname, r.tea_id) for r in db.execute("SELECT * FROM teacher").fetchall() ])
            params=[ (teachers[name], subjects[code], schoolyear, classes[klass]) for name, code, klass in teachings ]
            db.executemany("INSERT IGNORE INTO teacher_subject(ts_tea_id, ts_sub_id, ts_sy_id, ts_cl_id) VALUES (?,?,?,?)", params)

        conn.commit()
    finally:
        conn.close()