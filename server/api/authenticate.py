from flask import abort
from webargs import fields as parse_fields
from webargs.flaskparser import use_args
from flask_restplus import Namespace, Resource
from models.user import User

api = Namespace('authentication', description='User authentication')

login_fields = api.model('', {
    'login': fields.String(required=True),
    'password': fields.String(required=True)
})

login_parse_fields = api.model('', {
    'login': parse_fields.Str(required=True),
    'password': parse_fields.Str(required=True)
})

@api.route('/')
class AuthenticateAPI(Resource):
    @api.expect(login_fields)
    @api.response(200, 'Success')
    @api.response(403, 'Not authenticated')
    @use_args(login_parse_fields)
    def post(self, args):
        login = args['login']
        password = args['password']
        if login is None or password is None:
            return abort(404)
        logged_user = User.query.filter_by(login=login, password=password).first()
        if logged_user is None:
            return abort(400)
        return logged_user.id
