from flask import Blueprint, url_for
from flask_restplus import Api

from health import api as ns_health
from user import api as ns_user
from authenticate import api as ns_auth

@Property
def specs_url(self):
    """Monkey patch for HTTPS"""
    return url_for(self.endpoint('specs'), _external=True, _scheme='https')

Api.specs_url = specs_url

version = '1.0'
blueprint = Blueprint('api', __name__, url_prefix='/api/%s' % version)
api = Api(blueprint, version=version, title='API', description='bezumnoe.ru public API', doc='/doc/')


for ns in [ns_health, ns_auth, ns_user]:
    api.add_namespace(ns)
