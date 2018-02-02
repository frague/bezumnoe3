#!/usr/local/bin/python
import socket

print "Content-type: text/plain\n\n\n"

try:
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.connect(("localhost", 9000))
    data = "some data"
    sock.sendall(data)
    result = sock.recv(1024)
    print result
    sock.close()
except Exception as e:
    print e
