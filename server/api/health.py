from flask import Blueprint, abort
from shared.config import api_prefix

health = Blueprint('health', __name__)

@health.route('%sstatus' % api_prefix, methods=['GET'])
def api_health():
	print 'OK'
