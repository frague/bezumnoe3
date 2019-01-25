from flask import abort, session
from webargs import fields as parse_fields
from webargs.flaskparser import use_args
from flask_restplus import Namespace, Resource, fields
from models.user import UserModel
from shared.db import db
from api.authenticate import authenticated

api = Namespace('user', description='User api')

register_fields = api.model('UserModel', {
    'login': fields.String(required=True),
    'password': fields.String(required=True)
})

@api.route('/')
class Users(Resource):
    @api.expect(register_fields)
    @api.response(200, 'Success')
    @api.response(404, 'Authentication data not found')
    @use_args({
        'login': parse_fields.Str(missing=None, location='json'),
        'password': parse_fields.Str(missing=None, location='json')
    })
    def put(self, json):
        pass

    @authenticated
    def get():
        pass

login_fields = api.model('UserModel', {
    'login': fields.String(required=True),
    'password': fields.String(required=True)
})

@api.route('/<int:user_id>')
class User(Resource):
    @api.expect(login_fields)
    @use_args({
        'login': parse_fields.Str(missing=None, location='json'),
        'password': parse_fields.Str(missing=None, location='json')
    })
    def post(self, json, user_id):
        print(json)
        print(user_id)
        pass

    @authenticated
    def get(user_id):
        pass

    @authenticated
    def delete(user_id):
        pass
