import os
import uuid
from flask import Flask, send_from_directory
from waitress import serve
from api import blueprint as api
from shared.db import db

from models.user import User

mysql_host = os.environ.get('mysql_host', 'localhost')
mysql_user = os.environ.get('mysql_user', 'root')
mysql_pass = os.environ.get('mysql_pass')
mysql_db_name = os.environ.get('mysql_db_name', 'db')

app = Flask(__name__)
app.secret_key = os.environ.get('secret_key', uuid.uuid4().hex)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://%s:%s@%s/%s' % (mysql_user, mysql_pass, mysql_host, mysql_db_name)
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

#app.config['SQLALCHEMY_ECHO'] = True

db.init_app(app)
app.register_blueprint(api)

mysql_status = ''
with app.app_context():
    try:
        db.engine.execute('SET NAMES utf8')
        db.engine.execute('SET character_set_connection=utf8')
        mysql_status = '* Connection to DB: OK'
        #query = User.query.options()
        #for user in query:
        #    print user.login, user.id
    except Exception:
        print('Unable to connect to the database')
        mysql_status = '* Connection to DB: Failure'

with open('status.txt', 'a') as myfile:
    myfile.write(mysql_status.'\n')

@app.route('/', defaults={'path': 'index.html'})
@app.route('/<path:path>')
def index(path):
    return send_from_directory('../client/dist/client', path)

if __name__ == '__main__':
    #app.run(host='0.0.0.0', port=9000)
    serve(app, host='0.0.0.0', port=9000)
