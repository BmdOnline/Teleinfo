#!/usr/bin/python
# -*- coding: iso-8859-1 -*-
import serial
import datetime
import time
import MySQLdb

vADCO = 0
vOPTARIF =  »
vISOUSC = 0
vHCHC = 0
vHCHP = 0
vPTEC =  »
vIINST = 0
vIMAX = 0
vPAPP = 0
vHHPHC =  »
complet = 0

conn = MySQLdb.connect (‘localhost’,'root’,'motdepasse’,'nombasededonnees’)
curs = conn.cursor()
ser = serial.Serial(‘/dev/ttyUSB0', 1200, bytesize=7, parity=’E', stopbits=1, timeout=1)

line =  »
while (line== » and complet==0):
line = ser.readline().decode(‘utf-8')[:-2]
if line !=  »:
if line.startswith(‘ADCO’):
ADCO, vADCO, garbage = line.split(‘ ‘,2)
if line.startswith(‘OPTARIF’):
OPTARIF, vOPTARIF, garbage = line.split(‘ ‘,2)
if line.startswith(‘ISOUSC’):
ISCOUSC, vISOUSC, garbage = line.split(‘ ‘,2)
if line.startswith(‘HCHC’):
HCHC, vHCHC, garbage = line.split(‘ ‘,2)
if line.startswith(‘HCHP’):
HCHP, vHCHP, garbage = line.split (‘ ‘,2)
if line.startswith(‘PTEC’):
PTEC, vPTEC, garbage = line.split(‘ ‘,2)
if line.startswith(‘IINST’):
IINST, vIINST, garbage = line.split(‘ ‘,2)
if line.startswith(‘IMAX’):
IMAX, vIMAX, garbage = line.split(‘ ‘,2)
if line.startswith(‘PAPP’):
PAPP, vPAPP, garbage = line.split(‘ ‘,2)
if line.startswith(‘HHPHC’):
HHPHC, vHHPHC, garbage = line.split(‘ ‘,2)
if(vADCO!=0):
complet=1

t = datetime.datetime.now()
timestamp = time.mktime(t.timetuple())
timestamp = int(timestamp)
rec_date = t.strftime(« %Y-%m-%d »)
rec_time = t.strftime(« %H:%M:%S »)
vPTEC = vPTEC[:2]
line= »

print timestamp,rec_date,rec_time,vADCO,vOPTARIF,vISOUSC,vHCHP,vHCHC,vPTEC,vIINST,vIMAX,vPAPP,vHHPHC
curs.execute (‘INSERT INTO teleinfo (timestamp, rec_date, rec_time, adco, optarif, isousc, hchp, hchc, ptec, inst1, inst2, inst3, imax1, imax2, imax3, pmax, papp, hhphc, motdetat, ppot, adir1, adir2, adir3) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)’,(timestamp,rec_date,rec_time,vADCO,vOPTARIF,vISOUSC,vHCHP,vHCHC,vPTEC,vIINST,0,0,vIMAX,0,0,0,vPAPP,vHHPHC,’000000', »,0,0,0))

conn.commit()
curs.close()
conn.close()
ser.close()