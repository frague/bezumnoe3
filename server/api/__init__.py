from flask import Blueprint
from flask_restplus import Api

from health import api as ns_health
from authenticate import api as ns_auth

blueprint = Blueprint('api', __name__, url_prefix='/api/1.0')
api = Api(blueprint, version='1.0', title='API', description='bezumnoe.ru public API', doc='/doc/')

for ns in [ns_health, ns_auth]:
    api.add_namespace(ns)
