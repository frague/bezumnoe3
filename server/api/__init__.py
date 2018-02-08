from flask_restplus import Api, Resource, fields

from health import api as ns_health

api = Api(version='1.0', title='API', description='bezumnoe.ru public API')

for ns in [ns_health]:
    api.add_namespace(ns)