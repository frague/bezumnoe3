from flask import abort
from webargs import fields
from webargs.flaskparser import use_args
from flask_restplus import Namespace, Resource

api = Namespace('authentication', description='User authentication')

login_args = {
    'login': fields.Str(required=True),
    'password': fields.Str(required=True)
}

@api.route('/')
class AuthenticateAPI(Resource):
    @api.doc('authenticate_user')
    @use_args(login_args)
    def post(self, args):
        login = args['login']
        password = args['password']
        if login is None or password is None:
            return abort(404)
        return 'Aaaa!'
