from flask import send_from_directory
from flask_restplus import Namespace, Resource

api = Namespace('health', description='Health statistics')

@api.route('/')
class HealthAPI(Resource):
    @api.doc('get_status')
    def get(self):
        return send_from_directory('../../', 'status.txt');
