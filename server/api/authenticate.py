from flask import Blueprint, abort
from shared.config import api_prefix
from shared.utils import get_post

authenticate = Blueprint('authenticate', __name__)

@authenticate.route('%sauthenticate' % api_prefix, methods=['POST'])
def api_authenticate():
	login = get_post('login')
	password = get_post('password')
	if login is None or password is None:
		abort(404)
	print 'Aaaa!'
