#!/usr/local/bin/python3

"""
Will backup all the databases listed, will put files in same DIR as script'
To run: $ python dbbackup.py OR python3 dbbackup.py
"""

import configparser
import os
import time
import getpass

HOST='172.31.19.243'
PORT='3306'
DB_USER='tpglobalfx_u_test'
DB_PASS='7#Dq5@^dJC8$(&A1'
# if using one database... ('database1',)
databases=('cloud_tpglobalfx')

def get_dump(database):
    filestamp = time.strftime('%Y-%m-%d-%I')
    # D:/xampp/mysql/bin/mysqldump for xamp windows
    os.popen("mysqldump -h %s -P %s -u %s -p%s %s > %s.sql" % (HOST,PORT,DB_USER,DB_PASS,database,database+"_"+filestamp))

    print("\n|| Database dumped to "+database+"_"+filestamp+".sql || ")


if __name__=="__main__":
    for database in databases:
        get_dump(database)
