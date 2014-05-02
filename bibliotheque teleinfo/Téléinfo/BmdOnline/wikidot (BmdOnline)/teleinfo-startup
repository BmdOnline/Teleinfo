#! /bin/sh
### BEGIN INIT INFO
# Provides:          teleinfo
# Required-Start:    $remote_fs rmnologin
# Required-Stop:
# Default-Start:     1 2 3 4 5
# Default-Stop:
# Short-Description: Start Stop teleinfo
# Description:      
### END INIT INFO

NAME=teleinfo
DAEMON=/usr/local/bin/teleinfo

[ -x "$DAEMON" ] || exit 0

case "$1" in
  start)
	$DAEMON -m s -d /dev/teleinfo
	;;
  stop)
	/usr/bin/killall teleinfo
	;;
  restart|force-reload)
	/usr/bin/killall teleinfo;$DAEMON -m s -d /dev/teleinfo
	;;
  *)
	echo "Usage: $NAME {start|stop|restart|force-reload}" >&2
	exit 3
	;;
esac

:
