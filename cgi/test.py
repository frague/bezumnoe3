#!/usr/local/bin/python
from flask import Flask, request

app = Flask('daemon')

app.run('0.0.0.0', 4201)
