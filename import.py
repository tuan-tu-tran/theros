#!/usr/bin/env python
"""
A script to import initial data from ProEco into Theros
"""

import logging
import argparse

parser=argparse.ArgumentParser(description=__doc__)
parser.add_argument("worksFile", help="the csv file containing the raw works export from ProEco", metavar="CSV_FILE" , default="travaux.csv", nargs="?")
parser.add_argument("-v","--verbose", action="store_const", dest="logging", const=logging.DEBUG, default=logging.INFO, help="show debug logs")
args=parser.parse_args()
logging.basicConfig(level=args.logging)
logger=logging.getLogger()

worksFile=args.worksFile
works=[]
classes=set()
students=set()
with open(worksFile) as fh:
    header=True
    for line in fh:
        if header:
            header=False
            continue
        klass,student, dummy, foo, desc, grp = map(lambda s:s.strip(), line.split("\t"))
        klass=klass.replace(" ","")
        student=student.replace("  "," ")
        classes.add(klass)
        students.add(student)
        if desc:
            works.append((klass, student, desc))
            logger.debug("keep %s", works[-1])
        else:
            logger.debug("discarded line %s", line.strip())

logger.info("got %i works, %i students, %i classes", len(works), len(students), len(classes))

