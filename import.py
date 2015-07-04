#!/usr/bin/env python
"""
A script to import initial data from ProEco into Theros
"""

import logging
logging.basicConfig(level=logging.DEBUG)
logger=logging.getLogger()

worksFile="travaux.csv"
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

logger.debug("got %i works, %i students, %i classes", len(works), len(students), len(classes))

