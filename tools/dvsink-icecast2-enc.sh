#!/bin/sh

FFMPEG2THEORA=/home/rgareus/bin/ffmpeg2theora-0.27

#NOTE: most players won't ever show the meta-data
# VLC can catch and display meta-data updates while the 
# stream is continuing (mplayer can't)
#
# BTW: only title and artist are shown in live-streams
# date,location, etc are basically only required for archiving
#

exec $FFMPEG2THEORA \
  -v 4 --speedlevel 2 \
  --aspect 4:3 \
	--title "LAC 2011" \
	--location "Bewerunge Room, NUIM, Ireland " \
	--organization "Linux Audio Conference 2011" \
	--license "CC" \
	-o - -

exit

# example for archiving -> use `lactranscode.sh`
exec $FFMPEG2THEORA \
  -v 4 --speedlevel 2 \
  --aspect 4:3 \
	--title "Keynote" \
	--artist "Fons Adriaensen" \
	--date "2011-05-07 10:00:00" \
	--location "Bewerunge Room, NUIM, Ireland " \
	--organization "Linux Audio Conference 2011" \
	--license "CC" \
	-o - -
