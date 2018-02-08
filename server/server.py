import os
from flask import Flask
from api import blueprint as api
from shared.db import db

from models.user import User

mysql_host = os.environ.get('mysql_host', 'localhost')
mysql_user = os.environ.get('mysql_user', 'root')
mysql_pass = os.environ.get('mysql_pass')
mysql_db_name = os.environ.get('mysql_db_name', 'db')

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://%s:%s@%s/%s' % (mysql_user, mysql_pass, mysql_host, mysql_db_name)
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
#app.config['SQLALCHEMY_ECHO'] = True

print(mysql_host)

db.init_app(app)
app.register_blueprint(api)

with app.app_context():
    try:
        db.engine.execute('SET NAMES utf8')
        db.engine.execute('SET character_set_connection=utf8')

        query = User.query.options()
        for user in query:
            print user.login, user.id
    except Exception:
        print("Unable to connect to the database")


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=9000)
