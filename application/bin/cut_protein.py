# -*- coding: utf-8 -*-

from math import sqrt
import sys

translate = {'A':'ALA','C':'CYS','D':'ASP','E':'GLU','F':'PHE','G':'GLY','H':'HIS','I':'ILE','K':'LYS','L':'LEU','M':'MET','N':'ASN','P':'PRO','Q':'GLN','R':'ARG','S':'SER','T':'THR','V':'VAL','W':'TRP','Y':'TYR' }

ray = 10
project = '../../public/data/'+sys.argv[1]
chain = sys.argv[2]
residue = sys.argv[3]

filename = project+'/raw.pdb'

resname1 = residue[0]
resname = translate[resname1]
resnum = residue[1:]
atoms = []
residues = []
coord = []


file = open(filename)
save = open(project+"/origin.pdb","w")

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

print len(residues)

for atom in atoms:
	if atom[1] in residues:
		save.write(atom[5])

save.write("TER\nEND")
save.close()