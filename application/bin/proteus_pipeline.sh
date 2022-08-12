# Selecting sextets
python ../../application/bin/select_sextets.py $1 $2 

# Running SSV
nohup python ../../application/bin/ssv.py $1 $2 $3 > $1/log.txt 2>&1 &