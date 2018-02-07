from flask import request

def get_post(key, default=None):
	try:
		return request.form[key]
	except KeyError:
		return default;