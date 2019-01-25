DT=`date '+%d/%m/%Y %H:%M:%S'`
PIDS=`ps ax|grep server.py|grep python|grep -o '^[ ]*[0-9]*'`

echo "$DT: Server restarting:" > status.txt

if [ -z "$PIDS" ]; then
  echo "* Server is not running." >> status.txt
else
  for PID in $PIDS; do
    kill -9 $PID
    echo "* Shutting down the server: $PID" >> status.txt
  done
fi

source ~/.bashrc
source activate bzmn
echo "* Launching the fresh server instance" >> status.txt
pipenv install
pipenv run python server.py > /dev/null 2>&1 & disown
