#!/usr/local/bin/python
import requests

r = requests.get("http://0.0.0.0:5000")
print r.content