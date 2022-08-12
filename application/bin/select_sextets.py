# -*- coding: utf-8 -*-
# select_sextets.py
# Function: 
# Author: Diego Mariano
# Date: mar 28, 2018 / update jun 12, 2018


# -----------------------------------------------------------------
# Packages and modules
# -----------------------------------------------------------------
from math import sqrt
import sys


# -----------------------------------------------------------------
# Key variables
# -----------------------------------------------------------------
translate = {'ALA':'A','CYS':'C','ASP':'D','GLU':'E','PHE':'F','GLY':'G','HIS':'H','ILE':'I','LYS':'K','LEU':'L','MET':'M','ASN':'N','PRO':'P','GLN':'Q','ARG':'R','SER':'S','THR':'T','VAL':'V','TRP':'W','TYR':'Y' }
itranslate = {'A':'ALA','C':'CYS','D':'ASP','E':'GLU','F':'PHE','G':'GLY','H':'HIS','I':'ILE','K':'LYS','L':'LEU','M':'MET','N':'ASN','P':'PRO','Q':'GLN','R':'ARG','S':'SER','T':'THR','V':'VAL','W':'TRP','Y':'TYR' }

project = sys.argv[1]
pdb_name = '../../public/data/'+project+'/origin.pdb'
chain = sys.argv[2]
cutoff_min = 3.35
cutoff_max = 16.4
distance_matrix = []
residues_list = []
sextets = []


# -----------------------------------------------------------------
# Collecting residues in the original file
# -----------------------------------------------------------------
chain_origin = sys.argv[2]
residues_list_origin = []

try:
	pdb_origin_name = '../../public/data/'+project+'/raw.pdb'
	pdb_origin = open(pdb_origin_name)
except:
	pdb_origin_name = '../../public/data/'+project+'/origin.pdb'
	pdb_origin = open(pdb_origin_name)

lines = pdb_origin.readlines()
for line in lines:
	if line[0:4] == 'ATOM' and line[21] == chain_origin and line[13:16].strip() == 'CA':
		resnum_origin = line[22:27].strip()
		residues_list_origin.append(int(resnum_origin))


# -----------------------------------------------------------------
# Main
# -----------------------------------------------------------------

pdb = open(pdb_name)
lines = pdb.readlines()

# Construct distance matrix among CA
# First loop
for line in lines:
	if line[0:4] == 'ATOM' and line[21] == chain:
		atom_name = line[13:16].strip()

		if atom_name == 'CA':

			resnum = int(line[22:26].strip())
			residues_list.append(resnum)

			distance_vector = []
			del distance_vector [:]

			# coord
			x1 = float(line[30:38])
			y1 = float(line[38:46])
			z1 = float(line[46:54])

			# Second loop
			for line_2 in lines:

				if line_2[0:4] == 'ATOM':

					atom_name_2 = line_2[13:16].strip()

					if atom_name_2 == 'CA':

						# coord_2
						x2 = float(line_2[30:38])
						y2 = float(line_2[38:46])
						z2 = float(line_2[46:54])

						# euclidian distance
						ed = (x1 - x2)**2 + (y1 - y2)**2 + (z1 - z2)**2
						ed = sqrt(ed)

						distance_vector.append(ed)

			distance_matrix.append(distance_vector)

# PT-BR: Como a matriz eh espelhada, eh necessario apenas analisar uma parte dela
total_residues = len(residues_list)

# PT-BR: sem comparacoes entre vizinhos   
#aux = 1
aux = 3

for i in range(0,total_residues):

	for j in range(aux,total_residues):

		# PT-BR: sem comparacoes entre vizinhos | mudanca 21/01/2020 12:02 -> distancia minima entre pares passa a ser 3 residuos  
		#if residues_list[i] != residues_list[j]-1 and residues_list[i] != residues_list[j]-2 and residues_list[i] != residues_list[j]-3:

		# PT-BR: distancias foram obtidas no trabalho do jose
		if distance_matrix[i][j] >= cutoff_min and distance_matrix[i][j] <= cutoff_max:
			
			# PT-BR: verifica se os vizinhos estao armazenados no arquivo
			if residues_list[i]-1 in residues_list_origin and residues_list[i]+1 in residues_list_origin and residues_list[j]-1 in residues_list_origin and residues_list[j]+1 in residues_list_origin:
				sextets.append([residues_list[i],residues_list[j]])

	aux = aux + 1

#print len(sextets)


pdb_origin = open(pdb_origin_name)
lines = pdb_origin.readlines()

i = 0
for s in sextets:
	i += 1
	#print i
	w = open('../../public/data/'+project+"/tmp/"+str(s[0])+"_"+str(s[1])+".pdb",'w')
	
	for l in lines:
		if l[0:4] == 'ATOM' and l[21] == chain:
			resnum = int(l[22:26])
			atom_name = l[13:16].strip()
		
			if resnum == s[0]-1 or resnum == s[0] or resnum == s[0]+1 or resnum == s[1]-1 or resnum == s[1] or resnum == s[1]+1:
				if resnum == s[0] or resnum == s[1]:
					w.write(l)
				elif atom_name == 'C' or atom_name == 'CA' or atom_name == 'N' or atom_name == 'O':
					w.write(l)

	w.write("END")
	w.close()