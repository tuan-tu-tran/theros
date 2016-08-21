#!/usr/bin/env python
"""
A script to duplicate teachings from one year to another
"""

import logging
import argparse
import sys
import re
import hashlib
import os

parser=argparse.ArgumentParser(description=__doc__, formatter_class=argparse.ArgumentDefaultsHelpFormatter)
parser.add_argument("-s","--src", help="the source school year to duplicate from", metavar="SOURCE" , default="2014-15", dest="src")
parser.add_argument("-d","--dst", help="the destination school year to duplicate to", metavar="DEST" , default="2015-16", dest="dst")
parser.add_argument("-v","--verbose", action="store_const", dest="logging", const=logging.DEBUG, default=logging.INFO, help="show debug logs")
parser.add_argument("--dsn", help="the dsn to use for db operations", action="store", dest="dsn", default="theros_dev")
args=parser.parse_args()
logging.basicConfig(level=args.logging, stream=sys.stdout, format="%(levelname)7s : %(message)s")
logger=logging.getLogger()

if not args.dsn and args.insertData:
    parser.error("missing --dsn specification")

logging.info("connecting to database")
import pyodbc
conn=pyodbc.connect(dsn=args.dsn)
try:
    db=conn.cursor()
    schoolyears = dict(db.execute("SELECT sy_desc, sy_id FROM schoolyear").fetchall())
    if args.src not in schoolyears:
        parser.error("invalid src year: "+args.src)
    if args.dst not in schoolyears:
        parser.error("invalid dst year: "+args.dst)
    query="""
        INSERT IGNORE INTO teacher_subject(ts_tea_id, ts_sub_id, ts_cl_id, ts_sy_id)
        SELECT ts_tea_id, ts_sub_id, ts_cl_id, %i
        FROM teacher_subject
        WHERE ts_sy_id = %i
        ORDER BY ts_id
    """%(schoolyears[args.dst], schoolyears[args.src])
    logger.debug("executing query:\n%s", query.strip())
    db.execute(query)
    conn.commit()
finally:
    conn.close()

