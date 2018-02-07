from flask.views import MethodView

class StatusAPI(MethodView):

    def get(self):
    	return 'OK'