#!/usr/bin/env python
"""
A script to import initial data from ProEco into Theros
"""

import logging
import argparse
import sys
import re

parser=argparse.ArgumentParser(description=__doc__, formatter_class=argparse.ArgumentDefaultsHelpFormatter)
parser.add_argument("worksFile", help="the csv file containing the raw works export from ProEco", metavar="CSV_FILE" , default="travaux.csv", nargs="?")
parser.add_argument("-v","--verbose", action="store_const", dest="logging", const=logging.DEBUG, default=logging.INFO, help="show debug logs")
parser.add_argument("--dsn", help="the dsn to use for db operations", action="store", dest="dsn", default="theros_dev")
parser.add_argument("--is", "--insert-student", help="insert (ignoring duplicates) students into database (requires --dsn)", action="store_true", dest="insertStudents")
parser.add_argument("--ic", "--insert-class", help="insert (ignoring duplicates) classes into database (requires --dsn)", action="store_true", dest="insertClasses")
parser.add_argument("--iw", "--insert-work", help="insert (ignoring duplicates) works into database (requires --dsn)", action="store_true", dest="insertWorks")
args=parser.parse_args()
logging.basicConfig(level=args.logging, stream=sys.stdout)
logger=logging.getLogger()

if not args.dsn and (
        args.insertStudents
        or args.insertClasses
        or args.insertWorks
    ):
    parser.error("missing --dsn specification")

worksFile=args.worksFile
works=[]
classes=set()
students=set()
class Work:
    def __init__(self, klass, student, desc, line):
        self.klass=klass
        self.student=student
        self.desc=desc
        self.line=line

with open(worksFile) as fh:
    header=True
    for i,line in enumerate(fh):
        if header:
            header=False
            continue
        line=line.decode("utf8")
        klass,student, dummy, foo, desc, grp = map(lambda s:s.strip(), line.split("\t"))
        klass=klass.replace(" ","").upper()
        if not re.search(r"^\d[A-Z]+$", klass):
            raise ValueError, "line %i contains bad class: %s"%(i+1, klass)
        student=student.replace("  "," ")
        classes.add(klass)
        students.add(student)
        if desc:
            works.append(Work(klass, student, desc, i+1))
            logger.debug("keep %s", works[-1])
        else:
            logger.debug("discarded line %s", line.strip())

logger.info("got %i works, %i students, %i classes", len(works), len(students), len(classes))

if args.insertStudents or args.insertClasses or args.insertWorks:
    logging.info("connecting to database")
    import pyodbc
    conn=pyodbc.connect(dsn=args.dsn)
    try:
        db=conn.cursor()
        if args.insertStudents:
            logging.info("inserting students")
            params=[(s,) for s in sorted(students)]
            db.executemany("INSERT IGNORE INTO student(st_name) VALUES (?)", params)

        if args.insertClasses:
            logging.info("inserting classes")
            params=[(c,) for c in sorted(classes)]
            db.executemany("INSERT IGNORE INTO class(cl_desc) VALUES (?)", params)

        if args.insertWorks:
            logging.info("inserting works")
            classes=db.execute("SELECT * FROM class").fetchall()
            classes=dict([(c.cl_desc, c.cl_id) for c in classes])
            students=db.execute("SELECT * FROM student").fetchall()
            students=dict([(s.st_name, s.st_id) for s in students])
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
        conn.commit()
    finally:
        conn.close()
