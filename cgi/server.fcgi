#!/usr/bin/python
import sys
sys.path.insert(0, '/usr/local/lib/python2.7/site-packages/')

from flup.server.fcgi import WSGIServer
from test import app

class ScriptNameStripper(object):
   def __init__(self, app):
       self.app = app

   def __call__(self, environ, start_response):
       environ['SCRIPT_NAME'] = '/api'
       return self.app(environ, start_response)

app = ScriptNameStripper(app)

if __name__ == '__main__':
    WSGIServer(app).run()

