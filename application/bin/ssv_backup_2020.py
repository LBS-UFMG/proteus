#-*- coding: utf-8 -*- 
# search_SSV.py
# Function: This script implements the method of variation of structural signature for clusters search. In theory, if the SSV is less than 10 points, the RMSD of that cluster is close to 0.5.
# Author: Diego Mariano
# Date: mar 22, 2018


# -----------------------------------------------------------------
# Packages and modules
# -----------------------------------------------------------------
import MySQLdb # para o MySQL
import pymysql
import sys
from math import sqrt
import numpy as np
from scipy.spatial import distance
import glob
from Bio.PDB import *
from Bio.PDB.Atom import Atom
import time
import os


global cff
cff = 13


# -----------------------------------------------------------------
# Database
# -----------------------------------------------------------------
server = '150.164.203.92'
#server = '150.164.203.94'
user = 'root'
key = 'bio1nf0rule5*'
database = 'signatures'
# -----------------------------------------------------------------
con = MySQLdb.connect(server, user, key, port=3306)
con.select_db(database)
cursor = con.cursor()
# -----------------------------------------------------------------
# Obtaining matrix U from MySQL
cursor.execute("SELECT matrix_value from matrix_svd WHERE matrix_name = 'u' LIMIT 1")
row = cursor.fetchall()
U = row[0][0].split("\n")
# -----------------------------------------------------------------
# Obtaining subject from MySQL
cursor.execute("SELECT id_contact,signature,proteus_db from signatures")
signaturesdb = cursor.fetchall()
# -----------------------------------------------------------------


# -----------------------------------------------------------------
cursor.close()
# -----------------------------------------------------------------
				
# -----------------------------------------------------------------
# Functions
# -----------------------------------------------------------------

# Creating a structural signature
def generate_signature(pdb):

	signature_vector = []
	lines = open(pdb).readlines()

	for l1 in lines:

		if l1[0:4] == "ATOM" and (l1[12:16].strip() =='N' or l1[12:16].strip() =='O' or l1[12:16].strip() =='CA' or l1[12:16].strip() =='C'):

			x1 = float(l1[30:38])
			y1 = float(l1[38:46])
			z1 = float(l1[46:54])

			for l2 in lines:

				if l2[0:4] == "ATOM" and (l2[12:16].strip() =='N' or l2[12:16].strip() =='O' or l2[12:16].strip() =='CA' or l2[12:16].strip() =='C'):

					x2 = float(l2[30:38])
					y2 = float(l2[38:46])
					z2 = float(l2[46:54])

					# euclidian distance
					ed = (x1 - x2)**2 + (y1 - y2)**2 + (z1 - z2)**2
					ed = sqrt(ed)

					signature_vector.append(ed)

	return signature_vector

# Apply SVD reduction in the query
def qsvd(signature_full_vector, lines):


	# Creating empty numpy array
	cont = 0
	N = 0
	listU = []
	for line in lines:
		cont = cont + 1
		cell = line.split(",")
		if cont == 1:
			N = len(cell)
		listU.append(cell)

	U = np.zeros(shape=(cont, N))

	# converting list to numpy array
	for i in range(len(U)):
		for j in range(len(U[i])):
			U[i][j] = float(listU[i][j])


	# Calculating qtil
	signature_query = np.dot(U, signature_full_vector)

	return signature_query

# Calculate the strucutal signature variation (Euclidean distance)
def ssv(signature_query, rows):

	cutoff = cff
	clusters = []
	j = 0
	k = 0
	for row in rows:
		'''
		if j % 17500 == 0:
			k = k+10
			if k <= 100:
				print str(k)+'%'
		'''
		j = j+1
		signature_subject = row[1].split(',')
		signature_subject = [float(i) for i in signature_subject]

		ed = distance.euclidean(signature_query,signature_subject)

		if ed < cutoff:
			clusters.append([int(row[0]),str(row[2])])
	return clusters

# Suggest mutations
def show_mutations(clusters,pdb):

	mutations = []
	mutation_details = []

	# Getting original residue
	lines_r1 = open(pdb).readlines()
	line_r1 = lines_r1[5]
	lines_r2 = open(pdb).readlines()
	line_r2 = lines_r2[17]

	r1_pos = line_r1[23:26].strip()
	r2_pos = line_r2[23:26].strip()

	r1_name = line_r1[17:20].strip()
	r2_name = line_r2[17:20].strip()

	translate = {'ALA':'A','CIS':'C','CYS':'C','ASP':'D','GLU':'E','PHE':'F', 'FEN':'F', 'GLY':'G','GLI':'G','HIS':'H','ILE':'I', 'LYS':'K','LIS':'K', 'LEU':'L', 'MET':'M','ASN':'N','PRO':'P','GLN':'Q','ARG':'R','SER':'S','THR':'T','TRE':'T','VAL':'V','TRP':'W', 'TRI':'W','TYR':'Y','TIR':'Y' }

	r1_name = translate[r1_name]
	r2_name = translate[r2_name]
	site = ''

	for cluster in clusters:
		con.select_db(cluster[1])
		cursor = con.cursor()
		cursor.execute("SELECT id_contact,r1_name,r2_name,ctt_pdbid,ctt_chain,r1_position,r2_position from residue_contact where id_contact = %s" %(cluster[0]))
		row = cursor.fetchall()
		cursor.close()
		mut = r1_name+r1_pos+row[0][1]+"/"+r2_name+r2_pos+row[0][2]
		site = r1_name+r1_pos+"/"+r2_name+r2_pos

		if r1_name == row[0][1] and r2_name == row[0][2]:
			return mutations,mutation_details,site # no mutation
		elif r1_name == row[0][1]:
			mut = r2_name+r2_pos+row[0][2] # mutation in r2
		elif r2_name == row[0][2]:
			mut = r1_name+r1_pos+row[0][1] # mutation in r1
		if mut not in mutations:
			mutations.append(mut)
			# mutations details: 0 id_contact, 1 database, 2 pdbid, 3 chain, 4 position1, 5 position2, 6 r1_name, 7 r2_name
			mutation_details.append([row[0][0],cluster[1],row[0][3],row[0][4],row[0][5],row[0][6],row[0][1],row[0][2]]) 
	
	return mutations,mutation_details,site

def get_atoms(origin):

	PDB1 = PDBParser().get_structure('PDB1',origin)

	# Extracting atoms
	ref_atoms = [] #PDB1
	for ref_chain in PDB1[0]:
		for ref_res in ref_chain:
			for ref_a in ref_res:
				if ref_a.get_name() == 'C' or ref_a.get_name() == 'N' or ref_a.get_name() == 'CA' or ref_a.get_name() == 'O':
					ref_atoms.append(ref_a)


	return ref_atoms

def filter(origin, subject, mutation, id_project):

	mutation = mutation.replace("/","_")
	cutoff = 0.5
	database = subject[1]
	id_subject = subject[0]
	row = ''
	# subject atom
	con.select_db(database)
	cursor = con.cursor()
	cursor.execute("SELECT * from atom where id_ctt = %s" %(id_subject))
	row = cursor.fetchall()
	#row[0][0]
	atms = row
	cursor.close()
	atm_list = []
	for atm in atms:
		atom_parsed = parser_atom(atm)
		if atom_parsed is None:
			continue
		atm_list.append(atom_parsed)

	#atm_list = sorted(atm_list) #PDB2

	# Fazendo a sobreposicao
	si = Superimposer()
	si.set_atoms(origin, atm_list)

	if si.rms <= cutoff:

		itranslate = {'A':'ALA','C':'CYS','D':'ASP','E':'GLU','F':'PHE','G':'GLY','H':'HIS','I':'ILE','K':'LYS','L':'LEU','M':'MET','N':'ASN','P':'PRO','Q':'GLN','R':'ARG','S':'SER','T':'THR','V':'VAL','W':'TRP','Y':'TYR' }

		# save res 1
		con.select_db("protein_align")
		cursor = con.cursor()
		#cursor.execute("SELECT id_atm,res_id,name,level,bfactor,occupancy,element,serial_number,fullname,coord FROM  `atom` WHERE (res_id =  (SELECT r1_id FROM  `residue_contact` WHERE id_contact = %s) or res_id =  (SELECT r2_id FROM  `residue_contact` WHERE id_contact = %s)) and type = 0" %(id_subject,id_subject))
		cursor.execute("SELECT id_atm,res_id,name,level,bfactor,occupancy,element,serial_number,fullname,coord FROM  `atom` WHERE res_id = (SELECT r1_id FROM  `residue_contact` WHERE id_contact = %s) and type = 0" %(id_subject))
		row = cursor.fetchall()
		
		atms_res1 = row
		cursor.close()

		atm_list_res1 = []
		for atm in atms_res1:
			atom_parsed = parser_atom(atm)
			if atom_parsed is None:
				continue
			atm_list_res1.append(atom_parsed)

		# save res 2
		cursor = con.cursor()
		cursor.execute("SELECT id_atm,res_id,name,level,bfactor,occupancy,element,serial_number,fullname,coord FROM  `atom` WHERE res_id = (SELECT r2_id FROM  `residue_contact` WHERE id_contact = %s) and type = 0" %(id_subject))
		row = cursor.fetchall()
		
		atms_res2 = row
		cursor.close()

		atm_list_res2 = []
		for atm in atms_res2:
			atom_parsed = parser_atom(atm)
			if atom_parsed is None:
				continue
			atm_list_res2.append(atom_parsed)

		atm_final = atm_list + atm_list_res1 + atm_list_res2

		# New coords are inserted
		si.apply(atm_final)

		#getting residues name
		#R1
		cursor = con.cursor()
		cursor.execute("SELECT r1_name FROM `residue_contact` WHERE id_contact = %s" %(id_subject))
		row = cursor.fetchall()		
		r1_name = row[0][0]
		r1_name = itranslate[r1_name]
		cursor.close()

		#R2
		cursor = con.cursor()
		cursor.execute("SELECT r2_name FROM `residue_contact` WHERE id_contact = %s" %(id_subject))
		row = cursor.fetchall()		
		r2_name = row[0][0]
		r2_name = itranslate[r2_name]
		cursor.close()


		#rec pdb file
		try:
			os.mkdir('../../public/data/'+id_project+'/mutations')
		except:
			erro = "folder exists"

		w = open('../../public/data/'+id_project+'/mutations/'+mutation+'.pdb','w')
		i = -1 #atom
		j = 1 #residue
		atom_main = 1 #main chain of residues

		for z in range(24):
			if atom_main <= 24:

				i += 1 

				line = 'ATOM   '
				line += str(i+1)
				line += (5-len(str(i+1)))*" "
				line += atm_final[atom_main-1].get_fullname()
				if atom_main >= 5 and atom_main <= 8:
					line += " "+r1_name+" A "
				elif atom_main >= 17 and atom_main <= 20:
					line += " "+r1_name+" A "
				else:
					line += " ALA A "
				line += str(j)
				line += (8-len(str(j)))*" "
				line += str(round(atm_final[atom_main-1].get_coord()[0],3))
				line += (8-len(str(round(atm_final[atom_main-1].get_coord()[0],3))))*" "
				line += str(round(atm_final[atom_main-1].get_coord()[1],3))
				line += (8-len(str(round(atm_final[atom_main-1].get_coord()[1],3))))*" "
				line += str(round(atm_final[atom_main-1].get_coord()[2],3))
				line += (9-len(str(round(atm_final[atom_main-1].get_coord()[2],3))))*" "
				line += str(atm_final[atom_main-1].get_occupancy())
				line += (5-len(str(atm_final[atom_main-1].get_occupancy())))*" "
				line += str(atm_final[atom_main-1].get_bfactor())
				line += (16-len(str(atm_final[atom_main-1].get_bfactor())))*" "
				line += atm_final[atom_main-1].get_name()[0]
				line += "\n"

				w.write(line)

				atom_main += 1

				if atom_main == 9:
					for k in range(24,24+len(atm_list_res1)):

						i+=1

						line = 'ATOM   '
						line += str(i+1)
						line += (5-len(str(i+1)))*" "
						line += atm_final[k].get_fullname()
						line += " "+r1_name+" A "
						line += str(j)
						line += (8-len(str(j)))*" "
						line += str(round(atm_final[k].get_coord()[0],3))
						line += (8-len(str(round(atm_final[k].get_coord()[0],3))))*" "
						line += str(round(atm_final[k].get_coord()[1],3))
						line += (8-len(str(round(atm_final[k].get_coord()[1],3))))*" "
						line += str(round(atm_final[k].get_coord()[2],3))
						line += (9-len(str(round(atm_final[k].get_coord()[2],3))))*" "
						line += str(atm_final[k].get_occupancy())
						line += (5-len(str(atm_final[k].get_occupancy())))*" "
						line += str(atm_final[k].get_bfactor())
						line += (16-len(str(atm_final[k].get_bfactor())))*" "
						line += atm_final[k].get_name()[0]
						line += "\n"

						w.write(line)
						

				elif atom_main == 21:
					for k in range(24+len(atm_list_res1),24+len(atm_list_res1)+len(atm_list_res2)):

						i += 1
						line = 'ATOM   '
						line += str(i+1)
						line += (5-len(str(i+1)))*" "
						line += atm_final[k].get_fullname()
						line += " "+r1_name+" A "
						line += str(j)
						line += (8-len(str(j)))*" "
						line += str(round(atm_final[k].get_coord()[0],3))
						line += (8-len(str(round(atm_final[k].get_coord()[0],3))))*" "
						line += str(round(atm_final[k].get_coord()[1],3))
						line += (8-len(str(round(atm_final[k].get_coord()[1],3))))*" "
						line += str(round(atm_final[k].get_coord()[2],3))
						line += (9-len(str(round(atm_final[k].get_coord()[2],3))))*" "
						line += str(atm_final[k].get_occupancy())
						line += (5-len(str(atm_final[k].get_occupancy())))*" "
						line += str(atm_final[k].get_bfactor())
						line += (16-len(str(atm_final[k].get_bfactor())))*" "
						line += atm_final[k].get_name()[0]
						line += "\n"

						w.write(line)
				
				if atom_main%4==1 and atom_main!=1:
					j+=1

			else:
				break

		w.write("END")
		w.close()

		clash = calculate_clash(id_project,atm_final,mutation)

		return True,round(si.rms,2),clash
	else:
		return False,'',''


# Function created by Jose Renato to construct a atom object
def parser_atom(a):
	# type: (dict) -> Bio.PDB.Atom
	"""
		:param a: dict with atoms fields
		:return: Objeto Atom da classe Bio.PDB.Atom.Atom
	"""

	crd = a[9].replace("[", "").replace("]", "")

	crd = crd.split(',') if crd.find(';') == -1 else crd.split(';')
	coord = np.array(map(lambda x: round(float(x), 3), crd))
	try:
		atm = Atom(str(a[2]), coord, round(float(a[4]), 2),
				   round(float(a[5]), 2), ' ', str(a[8]),
				   int(a[7]), element=str(a[6]))
	except:
		atm = None
	return atm


def calculate_ddG(id_project,chain,mut):

	try:

		res = mut.split("/")

		if len(res) == 2:
			res1 = res[0]
			mut1 = res1[-1]
			res1 = res1[:-1]

			res2 = res[1]
			mut2 = res2[-1]
			res2 = res2[:-1]

			if os.path.isfile(id_project+'/raw.pdb'):
				f = 'raw.pdb'
			else:
				f = 'origin.pdb'

			#run maestro
			os.system("../../application/bin/maestro/maestro config.xml "+id_project+"/"+f+","+chain+" --evalmut='"+res1+"."+chain+"{"+mut1+"},"+res2+"."+chain+"{"+mut2+"}' > "+id_project+"/ddG.txt")

		elif len(res) == 1:
			res1 = mut[:-1]
			mut1 = mut[-1]

			#run maestro
			os.system("../../application/bin/maestro/maestro config.xml "+id_project+"/origin.pdb,"+chain+" --evalmut='"+res1+"."+chain+"{"+mut1+"}' > "+id_project+"/ddG.txt")

		with open(id_project+'/ddG.txt') as f:
			for l in f:
				ddG = l

		ddG = ddG.split("\t")

		return round(float(ddG[len(ddG) - 2]),3)
	except:
		return '-'

def calculate_clash(project,atoms_test,mutation):

	res = mutation.split("_")

	try:
		r1 = res[0]
		r1 = int(r1[1:-1])
		r2 = res[1]
		r2 = int(r2[1:-1])
	except:
		r1 = int(mutation[1:-1])
		r2 = ''

	PDB1 = PDBParser().get_structure('PDB1',project+"/origin.pdb")

	print mutation

	# Comparing atoms
	for ref_chain in PDB1[0]:
		for ref_res in ref_chain:

			# Do not compair to the mutation positions
			if ref_res.id[1] != r1 and ref_res.id[1] != r2:

				# foreach atom of origin
				for a1 in ref_res:

					# foreach atom of atm_final
					for a2 in atoms_test:

						if a2.get_name() != 'C' and a2.get_name() != 'CA' and a2.get_name() != 'O' and a2.get_name() != 'N':

							dist = a1 - a2

							if dist < 2:
								print "Residue origin clash: "
								print ref_res.id[1]
								print "Atom clash: "
								print a2.get_name()
								print "Distance: "
								print dist
								return 'Yes'

	return 'No'

# -----------------------------------------------------------------
# Main
# -----------------------------------------------------------------

project = sys.argv[1]
chain = sys.argv[2]

folder = project+'/tmp'
pdbs = glob.glob(folder+'/*.pdb')
total_pdbs = len(pdbs)
total_valid_mutations = 0

# Save data: total tests
pj_php = open(project+"/data.txt","a+")
pj_php.write(str(total_pdbs))
pj_php.close() 
pj_php = open(project+"/data.txt","a+")

# Save mutations / mutations_details
mt = open(project+"/mutations.csv","w")
mtd = open(project+"/mutations_details.csv","w")

i = 1
p = float(100)/float(total_pdbs)

x = 0 #id site
z = 0 #id mutation (for each site)

for pdb_name in pdbs:

	# Calculete status 0%-100%
	status = open(project+'/status.txt','w')
	status.write(str(int(i*p)))
	status.close()
	#print str(i)+"/"+str(len(pdbs))+": "+pdb_name+"\n"
	i += 1

	# Creating structural signature
	signature_full_vector = generate_signature(pdb_name)

	# Reducing signature using SVD
	signature_query = qsvd(signature_full_vector, U)

	# Determing the clusters based on SSV
	clusters = ssv(signature_query, signaturesdb)

	# Show mutations suggested
	mutations,mutations_details,site = show_mutations(clusters,pdb_name)

	# Get atoms of the PDB origin
	atoms_origin = get_atoms(pdb_name)

	# Mutations
	valid_mutations = []
	valid_mutations_details = []
	all_clash = []

	for j in range(len(mutations_details)):

		valid,rms_score,clash = filter(atoms_origin, mutations_details[j], mutations[j], project)

		if valid:
			mutations_details[j].append(rms_score)
			valid_mutations_details.append(mutations_details[j])
			valid_mutations.append(mutations[j])
			all_clash.append(clash)

		valid = False

	#print mutations
	print len(valid_mutations)
	if len(valid_mutations) != 0:

		print valid_mutations

		total_valid_mutations += len(valid_mutations)
		
		x += 1
		mt.write(str(x)+';'+site+';')
		

		for mutation,vmd,c in zip(valid_mutations,valid_mutations_details,all_clash):
			
			z += 1
			#mutations.csv: id_site;site;id_mutation[1..n];mutation[1..n];
			mt.write(str(z)+';'+mutation+';')

			# VMD (mutations details): 0 id_contact, 1 database, 2 pdbid, 3 chain, 4 position1, 5 position2, 6 r1_name, 7 r2_name, 8 RMSD
			#mutations_details.csv: id_mutation;mutation;pdb;chain;r1;r2;rmsd;ddg;crash;
			mtd.write(str(z)+';') #id_mutation
			mtd.write(mutation+';') #mutation
			mtd.write(str(vmd[2])+';') #pdb
			mtd.write(str(vmd[3])+';') #chain
			mtd.write(str(vmd[6])+str(vmd[4])+';') #r1
			mtd.write(str(vmd[7])+str(vmd[5])+';') #r2
			mtd.write(str(vmd[8])+';') #rmsd
			ddG = calculate_ddG(project,chain,mutation)
			mtd.write(str(ddG)+';') #ddg
			mtd.write(c+';') #clash
			mtd.write("\n") #chain
			
		mt.write("\n")

pj_php.write("\n"+str(total_valid_mutations))

status = open(project+'/status.txt','w')
status.write(str(100))
status.close()
pj_php.close()
mt.close()
mtd.close()