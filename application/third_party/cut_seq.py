# -*- coding: utf-8 -*-
# cut_seq.py
# Function: 
# Author: Diego Mariano
# Date: mar 28, 2018


# -----------------------------------------------------------------
# Packages and modules
# -----------------------------------------------------------------
from math import sqrt
import sys


# -----------------------------------------------------------------
# Key variables
# -----------------------------------------------------------------
translate = {'ALA':'A','CYS':'C','ASP':'D','GLU':'E','PHE':'F','GLY':'G','HIS':'H','ILE':'I','LYS':'K','MET':'M','ASN':'N','PRO':'P','GLN':'Q','ARG':'R','SER':'S','THR':'T','VAL':'V','TRP':'W','TYR':'Y' }
itranslate = {'A':'ALA','C':'CYS','D':'ASP','E':'GLU','F':'PHE','G':'GLY','H':'HIS','I':'ILE','K':'LYS','M':'MET','N':'ASN','P':'PRO','Q':'GLN','R':'ARG','S':'SER','T':'THR','V':'VAL','W':'TRP','Y':'TYR' }

ray = 10

filename = "1bga.pdb"
filename = sys.argv[2]
chain = "A"
chain = sys.argv[3]
residue = "E166"
residue = sys.argv[4]

resname = residue[0]
resname = itranslate[resname]
resnum = residue[1:]
atoms = []
residues = []
coord = []

file = open(filename)

# New file
part_filename = filename.split(".pdb")
name_reduced_pdb = part_filename[0]+"_"+chain+"_"+residue+".pdb"
save = open(name_reduced_pdb,"w")


# -----------------------------------------------------------------
# Main
# -----------------------------------------------------------------

lines = file.readlines()

for line in lines:
	if line[0:4] == "ATOM" and line[21] == chain:
		atoms.append([line[17:20], line[22:26].strip(), float(line[30:38]), float(line[38:46]), float(line[46:54]), line])

		if resname == line[17:20] and resnum == line[22:26].strip():
			coord.append([float(line[30:38]), float(line[38:46]), float(line[46:54])])

for c in coord:

	x = c[0]
	y = c[1]
	z = c[2]

	for atom in atoms:

		# euclidian distance
		ed = (x - atom[2])**2 + (y - atom[3])**2 + (z - atom[4])**2
		ed = sqrt(ed)

		if ed < ray:
			if atom[1] not in residues:
				residues.append(atom[1])

for atom in atoms:
	if atom[1] in residues:
		save.write(atom[5])

save.write("TER\nEND")
save.close()