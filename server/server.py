from flask import Flask
from models.user import User
from shared.db import db

from api.authenticate import authenticate

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://frague_mysql:+MU7qAqh@frague.mysql/frague_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db.init_app(app)

app.register_blueprint(authenticate)

with app.app_context():
    query = User.query.options()
    for user in query:
        print user.login, user.id
