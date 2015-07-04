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
args=parser.parse_args()
logging.basicConfig(level=args.logging, stream=sys.stdout)
logger=logging.getLogger()

if not args.dsn and (
        args.insertStudents
        or args.insertClasses
    ):
    parser.error("missing --dsn specification")

worksFile=args.worksFile
works=[]
classes=set()
students=set()
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
            works.append((klass, student, desc))
            logger.debug("keep %s", works[-1])
        else:
            logger.debug("discarded line %s", line.strip())

logger.info("got %i works, %i students, %i classes", len(works), len(students), len(classes))

if args.insertStudents or args.insertClasses:
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
        conn.commit()
    finally:
        conn.close()
