from flask import Flask
from models.user import User
from shared.db import db
from shared.config import api_prefix

from api.authenticate import AuthenticateAPI
from api.health import StatusAPI

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://frague_mysql:+MU7qAqh@frague.mysql/frague_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

app.add_url_rule('%sstatus' % api_prefix, view_func=StatusAPI.as_view('status'))
app.add_url_rule('%sauthenticate' % api_prefix, view_func=AuthenticateAPI.as_view('authenticate'))

db.init_app(app)

with app.app_context():
#    query = User.query.options()
#    for user in query:
#        print user.login, user.id
	pass


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=9000)
