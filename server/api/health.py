from flask_restplus import Namespace, Resource, fields

api = Namespace('health', description='Health statistics')

@api.route('/status')
class HealthAPI(Resource):
    @api.doc('get_status')
    def get(self):
    	return 'OK'