from flask import abort
from flask.views import MethodView
from shared.utils import get_post

class AuthenticateAPI(MethodView):

    def post(self):
        login = get_post('login')
        password = get_post('password')
        if login is None or password is None:
            abort(404)
        return 'Aaaa!'
