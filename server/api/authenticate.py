import hashlib
import uuid
from datetime import datetime
from flask import abort, session
from webargs import fields as parse_fields
from webargs.flaskparser import use_args
from flask_restplus import Namespace, Resource, fields
from models.user import User
from shared.db import db

api = Namespace('authentication', description='User authentication')

login_fields = api.model('User', {
    'login': fields.String(required=True),
    'password': fields.String(required=True)
})

post_args = {
    'login': parse_fields.Str(missing=None),
    'password': parse_fields.Str(missing=None)
}

@api.route('/')
class AuthenticateAPI(Resource):
    @api.expect(login_fields)
    @api.response(200, 'Success')
    @api.response(404, 'Authentication data not found')
    @use_args(post_args, locations=('json', ))
    def post(self, form):
        login = form['login']
        password = form['password']
        bzmn = session.get('bzmn', None)

        if login is not None and password is not None:
            # Authenticate via login & password
            password = hashlib.md5(password).hexdigest()
            print('Auth via login & password: %s:%s' % (login, password))
            logged_user = User.query.filter_by(login=login, password=password).first_or_404()
            logged_user.session = uuid.uuid4().hex
            session['bzmn'] = logged_user.session

        elif bzmn is not None:
            # Authentication via session token
            print('Auth via session: %s' % (bzmn, ))
            logged_user = User.query.filter_by(session=bzmn).first_or_404()

        else:
            # Not found
            return abort(404)

        logged_user.session_pong = datetime.now().isoformat(' ')
        db.session.add(logged_user)
        db.session.commit()

        return logged_user.id
