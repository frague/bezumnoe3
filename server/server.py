from flask import Flask
from models.user import User
from shared.db import db

from api.authenticate import authenticate
from api.health import health

app = Flask(__name__)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://frague_mysql:+MU7qAqh@frague.mysql/frague_db'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

app.register_blueprint(authenticate)
app.register_blueprint(health)

db.init_app(app)

with app.app_context():
#    query = User.query.options()
#    for user in query:
#        print user.login, user.id
	pass


if __name__ == '__main__':
    app.run(port=9000)
