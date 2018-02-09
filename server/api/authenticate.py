import hashlib
import uuid
from datetime import datetime
from flask import abort
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
    'login': parse_fields.Str(required=True),
    'password': parse_fields.Str(required=True)
}

cookies_args = {
    'bzmn': parse_fields.Str(required=True)
}

@api.route('/')
class AuthenticateAPI(Resource):
    @api.expect(login_fields)
    @api.response(200, 'Success')
    @api.response(403, 'Not authenticated')
    @use_args(post_args, locations=('form', ))
    @use_args(cookies_args, locations=('cookies', ))
    def post(self, form, cookies):
        login = form['login']
        password = form['password']
        session = cookies['bzmn']

        if bzmn is not None:
            # Authentication via session token
            print('Auth via session: %s' % (session, ))
            logged_user = User.query.filter_by(session=session).first_or_404()

        elif login is not None and password is not None:
            # Authenticate via login & password
            password = hashlib.md5(password).hexdigest()
            print('Auth via login & password: %s:%s' % (login, password))
            logged_user = User.query.filter_by(login=login, password=password).first_or_404()
            logged_user.session = uuid.uuid4().hex

        else:
            # Not found
            return abort(400)

        logged_user.session_pong = datetime.datetime.now().isoformat(' ')
        db.session.add(logged_user)
        db.session.commit()

        return logged_user.id
