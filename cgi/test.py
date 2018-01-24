#!/usr/local/bin/python
import requests

print "Content-type: text/html\n\n"

try:
  r = requests.get("http://localhost:5000/")
except Exception as e:
  print e
else:
  print r.content