.. _1.0.1/shape-it:

##########
|shape-it|
##########

.. contents:: Table of contents
   :backlinks: none

.. admonition:: Copyright

   |copy| Copyright 2012 by Silicos-it, a division of Imacosi BVBA

   Shape-it is free software; you can redistribute it and/or modify
   it under the terms of the GNU Lesser General Public License as published 
   by the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Shape-it is distributed in the hope that it will be useful,
   but without any warranty; without even the implied warranty of
   merchantability or fitness for a particular purpose. See the
   `GNU Lesser General Public License <http://www.gnu.org/licenses/>`_
   for more details.

   Shape-it is linked against **OpenBabel** version 2. 
   **OpenBabel** is free software; you can redistribute it and/or modify 
   it under the terms of the GNU 
   General Public License as published by the Free Software Foundation 
   version 2 of the License.
   

.. admonition:: Version

   Shape-it 1.0.1

.. admonition:: Feedback

   Join our `Google groups community 
   <http://groups.google.com/group/silicos-it-chemoinformatics>`_
   to talk about inconsistencies, errors, raise questions or to make suggestions 
   for improvement.
   




.. highlight:: console

.. _1.0.1/shapeit_introduction:

************
Introduction
************

|shape-it| is a tool that aligns a reference molecule against a set of database molecules 
using the shape of the molecules as the align criterion. It is based on the use of Gaussian volumes 
as descriptor for molecular shape as it was introduced by Grant and Pickup [#grant]_.

|shape-it| is a program that is instructed by means of command line options. 
The program expects a single reference molecule (with three-dimensional coordinates) and a database 
file containing one or more molecules (with three-dimensional coordinates) that need to be shape-aligned
onto the reference molecule. The tool returns all aligned database molecules and their respective
shape overlap scores, or the top-best scoring molecules.


.. _1.0.1/shapeit_background:

**********
Background
**********

|shape-it| is based on the computation of the overlap between two molecules when their atomic 
volumes are represented using a Gaussian function. The rationale is to find the single alignment of 
reference and database molecule in which their volume overlap is maximized. In the next sections, the 
methodology is explained in more detail.


.. _1.0.1/shapeit_atom_gaussians:

Atom Gaussians
==============

The basis of the shape-based alignment procedure is the modeling of the atomic volume 
by a Gaussian representation. The atomic Gaussian is modeled with the parameters *m*, the center 
or position of the atom, and :math:`\alpha`, the spread. :math:`\alpha` is chosen to be inverse 
proportional to the squared radius of the atom.

The volume of an atom in this representation is computed as:

.. raw:: html

   $$ V_a = \int p \exp (- \alpha (m - r)^2) dr = p \sqrt{(\frac{\pi}{\alpha})^3} $$

with :math:`V_a` being the atomic Gaussian volume, *p* the normalization constant to scale the 
total volume to a level that is in relation to atomic volumes, *m* being the center of 
the Gaussian, *r* being the distance variable that is integrated, and *p* the normalization 
constant to scale the total volume to a level in relation to the atomic volume when
represented by a hard sphere volume of :math:`4 \pi R^3 / 3`.


.. _1.0.1/shapeit_gaussian_volume:

Gaussian volume
===============

The total volume of a molecule can be written in terms of atom volumes and higher-order 
overlaps as:

.. raw:: html

   $$ V = \sum_a V_a - \sum_{a,b} V_{ab} + \sum_{a,b,c} V_{abc} - \sum_{a,b,c,d} V_{abcd} + \dotsb $$
 
The interesting part of using Gaussians as representation for atoms is that the overlap 
between two atom Gaussians (*a*, :math:`\alpha`) and (*b*, :math:`\beta`) is a new 
Gaussian with:

* center :math:`m = (a \alpha + b \beta) / (\alpha + \beta)`
* spread :math:`\gamma = \alpha + \beta`

The overlap volume itself is given by the following formula:

.. raw:: html

   $$ V_{ab} = pp \exp (-\frac{\alpha \beta}{\alpha + \beta} \lVert a - b \rVert ^2) 
   \sqrt{(\frac{\pi}{\alpha + \beta})^3} $$
 
Since the overlap of two Gaussians is a new Gaussian, it is possible to implement an iterative 
procedure to compute the higher level overlaps as needed in the volume estimation. 
The procedure starts with representing all atoms and all atom-atom overlaps as Gaussians. 
Subsequently, extending the atom-atom overlaps with all atoms generates the next level 
of overlaps. In the current implementation of |shape-it| this procedure is repeated up to 
6 levels).

The outlined procedure is combinatorial in nature; therefore, it is necessary to limit the number 
of potential overlaps by defining a cutoff on the relative volume overlap, rather than on 
on the actual distance between two atoms since the overlap immediately takes into account 
the :math:`\alpha` of the Gaussians. The criterion for accepting an overlap is defined as:

.. raw:: html

   $$ \frac{V_{ab}}{V_a + V_b - V_{ab}} \geq 0.03 $$
 
.. note::
   
   Analysis of the computed volume overlaps has indicated that a cutoff of 0.03 and a maximum 
   level of 6 overlap volumes gives a good approximation of the actual hard-sphere volume 
   overlap.


.. _1.0.1/shapeit_molecule_molecule_overlap:

Molecule-molecule overlap
=========================

Given the description of the Gaussian volume of two molecules (**A** and **B**), it is possible to 
compute the overlap as follows:

.. raw:: html
   
   $$ V_{A,B} = \sum_{a \in A, b \in B} V_{ab} - \sum_{a,b \in A, c \in B} V_{abc} - 
   \sum_{a \in A, b,c \in B} V_{abc} + \sum_{a,b \in A, c,d \in V} V_{abcd} - \dotsb $$
 
Again, the procedure starts with all possible atom-atom overlaps and works from there further 
through all possible overlaps. An overlap is possible if the Gaussians that make up the overlap 
exist. For instance, to compute the overlap :math:`V_{abc}`, this is only possible if 
:math:`V_{ab}`, :math:`V_{ac}`, and :math:`V_{bc}` exist. By eliminating overlaps as early 
as possible it reduces significantly the number of combinations to process.

One important remark to be made at this point is that the overlap of a molecule with itself 
will not give the same value as the total volume of the molecule. This is due to the nature 
of the Gaussian overlap. The parameterization of the Gaussians was chosen such that the overlap 
of an atom with itself produces the hard sphere volume. However, overlapping the same atom more 
than twice with itself will give a deviating value of the hard sphere volume. Therefore, it is 
important to compute the self-overlap of a molecule as well in order to get a correct reference 
value.



.. _1.0.1/shapeit_optimal_alignment:

Optimal alignment
=================

Initial orientation
-------------------

The actual alignment procedure starts from a good initial guess. At first, the two 
molecules are aligned such that their respective center of mass and their principal axes 
coincide.

If there are *N* atoms and *N* overlap Gaussians, the center of mass is computed as:

.. raw:: html

   $$ (m_x, m_y, m_z) = \frac{1}{V}(\sum_{i \le N} x_iV_i, \sum_{i \le N} y_iV_i, \sum_{i \le N} z_iV_i) $$

The principal axes of the molecule are the eigenvectors of the singular value decomposition 
of the mass matrix of higher-order moments:

.. raw:: html
   
   $$ M = \frac{1}{V} \begin{bmatrix} \sum_{i \le N} V_ix_ix_i \sum_{i \le N} V_ix_iy_i \sum_{i \le N} V_ix_iz_i \\
                                      \sum_{i \le N} V_iy_ix_i \sum_{i \le N} V_iy_iy_i \sum_{i \le N} V_iy_iz_i \\
                                      \sum_{i \le N} V_iz_ix_i \sum_{i \le N} V_iz_iy_i \sum_{i \le N} V_iz_iz_i \end{bmatrix} $$

By translating the molecules such that their centers of mass coincides, one can
rewrite the alignment as a rigid-body rotation. To facilitate the rotational optimization, quaternion 
algebra is introduced [#karney]_. The formulas for the volume overlap are hence rewritten as:

.. raw:: html

   $$ V_{ab} = p p \sqrt{(\frac{\pi}{\alpha + \beta})^3} \exp(-\frac{\alpha\beta}{\alpha + \beta}q' A q) $$

where *q* is a unit quaternion representing the rotation and *A* is a matrix that solely depends 
on the position of the two centers of Gaussians *a* and *b*.


Gradient-ascent
---------------

Since the quaternion of the rotation should have a unit norm, the optimization problem is 
actually a constrained one with elliptic constraints. The procedure used here is based on 
a procedure outlined by Hasan and Hasan [#hasan]_.

The update formula can be rewritten, using Lagrange multipliers, as:

.. raw:: html
   
   $$ q_{i+1} = q_i + \alpha h $$

with:

.. raw:: html

   $$ h = \nabla_q V_{ab}(q) - 2\lambda q $$
 
and:

.. raw:: html
   
   $$ \alpha = -(h'({\nabla_q}^2 V_{ab}(q)-\lambda)h)^{-1} h' h $$
 
and the Lagrange coefficient is:

.. raw:: html

   $$ \lambda = q' \nabla_q V_{AB}(q) $$
 
Computing gradient and Hessian turns out to be rather straightforward. The gradient is:

.. raw:: html

   $$ \nabla_q V_{ab}(q) = -2 V_{ab} A q $$

and the Hessian is:

.. raw:: html

   $$ {\nabla_q}^2 V_{ab}(q) = 2 V_{ab} (2 A q q' A - A) $$
 
which only depends on the already computed volume, the matrix *A* and the quaternion *q*.


Simulated annealing
-------------------

The gradient-ascent gives in most situations satisfactory results; however in a few cases the procedure 
gets stuck in a local optimum. To overcome this problem an additional simulated annealing 
approach has been implemented as well. The overall simulated annealing procedure is outlined 
in the following scheme::

    Start from best rotor quaternion found
    While ( i < nbr_iter )
    {
        T = sqrt(i/ΔT); 
        Perturbate rotor quaternion
        Compute volume overlap Vnew
        If ( Vnew > Vbest )
        {
            Vbest = Vnew; update best_rotor;
        }
        If ( exp(-sqrt((Vold-Vnew)/T)) > random(0,1) )
        { 
            Vold = Vnew; keep rotor quaternion;
        }
    }
    Return Vbest, best_rotor;

The most important parameters of the simulated annealing procedure are the number of 
iterations and the temperature. The number of iterations is a user-defined parameter 
that can be set using the :option:`--addIterations` option. The temperature is adapted 
each iteration based on a :math:`\Delta T`, which is set to 1.1. This value for :math:`\Delta T`
is deduced from several test runs and gives good optimization properties.


.. _1.0.1/shapeit_alignment_scores:

Alignment scores
================

Scores
------

Given the total volume overlap of the atoms :math:`V_a` and the respective self-overlap 
volumes of the reference (:math:`V_{ra}`) and database molecule 
(:math:`V_{da}`), different overlap scores can be computed:

* Tanimoto: :math:`s = V_a / (V_{ra} + V_{da} - V_a)`
* Tversky_Ref: :math:`s = V_a / (0.95 V_{ra} + 0.05 V_{da})`
* Tversky_Db: :math:`s = V_a / (0.05 V_{ra} + 0.95 V_{da})`


Score file format
-----------------

The score file is a tab-delimited text file with 8 columns. The first line is a header 
line containing the tags of the columns. Using the definitions from the previous 
paragraph, the 8 columns are ordered and labeled as follows:

1. ``dbName`` (identifier of database molecule)
2. ``refName`` (identifier of the reference molecule)
3. ``Shape-it::Tanimoto``
4. ``Shape-it::Tversky_Ref``
5. ``Shape-it::Tversky_Db``
6. ``Overlap`` (effective overlap volume of the atoms)
7. ``refVolume`` (volume of reference molecule)
8. ``dbVolume`` (volume of database molecule)


.. _1.0.1/shapeit_usage:

*****
Usage
*****

.. _1.0.1/shapeit_command_line_interface:

Command line interface
======================

|shape-it| is run from the command line as follows::

   > shape-it [options]

Options can be either *required* or *optional*. Specifying no option, or specifying the 
:option:`-h` or :option:`--help` options generates a help message:

.. literalinclude:: /_static/shape-it.txt


The option :option:`-v` or :option:`--version` will print the version of the program and 
the version of the **OpenBabel** 
libraries against which |shape-it| has been linked.

The following sections describe in detail the different options.


.. _1.0.1/shapeit_command_line_options:

Required command line options
=============================


Reference molecule
------------------

The name of the file containing the reference molecule is specified using the 
:option:`-r` or :option:`--reference` option::

   > shape-it -r filename.ext [other_options]

or::

   > shape-it --reference filename.ext [other_options]

The reference file should contain at least one molecule specified as a connection table 
with 3D coordinates. Only the first molecule in the reference file will be used as a reference. 
The remainder of the file is neglected. The format of the input file should be one of the 
input formats recognized by **OpenBabel**.
The file format is either recognized from the file extension directly, or specified
using the :option:`-f` option.
If the filename carries the extension :file:`.gz`, the file is assumed to be an gzipped file
and is processed as well.


Database molecules
------------------

The file or list of files containing the database molecules is specified using the 
:option:`-d` or :option:`--database` option::

   > shape-it –d dbase.ext [other options]

or::

   > shape-it --database dbase.ext [other options]

The database file is a molecule file that could be possibly gzipped. The extension of 
the database file is used to guess the type. The format of the input file should be one 
of the input formats recognized by **OpenBabel**.
The file format is either recognized from the file extension directly, or specified
using the :option:`-f` option.



.. _1.0.1/shapeit_output_options:

Output options
==============

There are a two possible ways to return the results. The results can be appended to 
the aligned molecules as a molecule file or as a tab-delimited output file containing the scores. 
At least one of these two options should be specified, otherwise an error message will be 
printed.

Output molecules
----------------

The name of the molecule file to which the molecules are written after alignment to the 
reference molecule is specified using the :option:`-o` or :option:`--out` option::

   > shape-it -o filename.ext [other_options]

or::

   > shape-it --out filename.ext [other_options]

The output file contains first the reference molecule followed by the molecules aligned 
to the reference molecule. If the :option:`--best` option is used only the top-best scoring 
molecules will be reported in the output file, otherwise all molecules are written to output 
file. 

The file format is either recognized from the file extension directly, or specified
using the :option:`-f` option.

Each molecule in the molecule file contains an additional list of properties in which the 
respective scores are reported. These fields all start with the tag ``Shape-it::``.


Score file
----------

The resulting scores are written to a tab-delimited text file with the options 
:option:`-s` or :option:`--scores`::

   > shape-it -s filename [other_options]

or::

   > shape-it --scores filename [other_options]

The score file contains several columns in which different scores are represented. 
A more detailed description of the different scores is given in the 
:ref:`alignment scores <1.0.1/shapeit_alignment_scores>` section.


.. _1.0.1/shapeit_optional_command_line_options:

Optional command line options
=============================

List of best scoring molecules
------------------------------

When only the *N* best scoring molecules need to be reported, the :option:`--best` option 
is used::

   > shape-it --best N [other_options]

With this option set, |shape-it| will process all molecules and keep track of the best scoring 
ones. The criterion for ‘best’ scoring is defined using the :option:`--rankBy` option.


No alignment
------------

If the option :option:`--scoreOnly` is provided, the database molecules are not aligned to 
the reference molecule::

   > shape-it --scoreOnly [other_options]

In this case, only the volume overlap of the given poses with the reference molecule is computed 
and no optimal alignment is pursued. 


Ranking
-------

The reported molecules can be selected or ranked based on a specific score. This is 
done with the :option:`--rankBy` option::

   > shape-it --rankBy <TANIMOTO|TVERSKY_DB|TVERSKY_REF> [other_options]

The type of score by which molecules are ranked or selected is defined by a code:

* ``TANIMOTO``: Taninoto
* ``TVERSKY_REF``: Reference Tversky
* ``TVERSKY_DB``: Database Tversky

The formulas of these scores are given in :ref:`alignment scores <1.0.1/shapeit_alignment_scores>`. 
The code is not case-sensitive, therefore uppercase or lowercase forms can be used. 
This option has an effect when the :option:`--best` option is selected to determine the *N* 
best scoring molecules.

The default score by which molecules are selected or sorted is the Tanimoto score (``TANIMOTO``).


Additional iterations
---------------------

By default only a gradient-ascent step is performed starting from the initial orientations. If the 
option :option:`--addIterations` is specified, an additional *N* steps of a simulated annealing 
steps are performed::

   > shape-it --addIterations N [other_options]

This option slows down the program, but might give improved alignments when the gradient-ascent 
method gets stuck in a local optimum.


Cutoff
------

To report only molecules with an alignment score higher then a given cutoff value, the 
:option:`--cutoff` options is used::

   > shape-it --cutoff v [other_options]

The cutoff value should be between 0 and 1.0. By default the value is set to 0, which means 
all molecules will be reported.


File formats
------------

The format of the reference (:option:`-r` or :option:`--reference`) molecular file, 
the database (:option:`-d` or :option:`--database`) file and the output file
(:option:`-o` or :option:`--out`) can be specified with the :option:`-f` or :option:`--format`
option::

	> shape-it --format sdf [other_options]

The format specification should be one of the formats as recognised by **OpenBabel**::

	> obabel -L formats

If this option is not specified, then the respective formats are extracted
from corresponding file extensions. This is the default behavior.



.. _1.0.1/shapeit_summary:

********************
Command line summary
********************

Summary of the command line arguments to |shape-it|::

   GENERAL
   -------
   [O] -h, --help            N/A    Provides a short description of usage.
   [O] --info                N/A    Provides a detailed description for each option.
   [O] -q, --quiet           N/A    If this flag is set, minimum output is given to
                                    the user during execution of the program.
   [O] -v, --version         N/A    Provides the version of the program.

   INPUT
   -----
   [O] -r, --reference         -    Defines the reference molecule or pharmacophore
                                    that will be used to screen and/or align a
                                    database.
   [R] -d, --dbase             -    Defines the database that will be screened and/or
                                    aligned.

   OUTPUT
   ------
   [O] -o, --out               -    The transformed database molecules after aligning
                                    them to the reference pharmacophore.
   [O] -s, --scores            -    Tab-delimited text file with for each molecule the
                                    number of corresponding pharmacophore points and
                                    the overlap scores.
   [O] --cutOff              0.0    Minimum score for a structure to be reported.
   [O] --best                  0    Only best scoring molecules are reported.
   [O] --rankBy         TANIMOTO    Define scoring used by --cutOff and --best.
   [O] --noRef               N/A    Do not include the reference molecule in the final
                                    output file.

   OPTIONS
   -------
   [O] --scoreOnly           N/A    Flag to indicate that the volume overlap should be
                                    computed from the given poses and that no 
                                    translational or rotational optimization should be
                                    done.
   [O] --addIterations         0    Perform N additional optimization steps with the 
                                    simulated annealing procedure.
   [O] -f, --format            -    Specifies the format of the database, reference and
                                    output molecule files.


************
Installation
************

Installation of the |shape-it| program relies on the libraries of 
**OpenBabel** version 2.3. 
Installation of **OpenBabel** is exemplified in the 
:ref:`Configuring OS X for chemoinformatics <configuring_osx_for_chemoinformatics>` 
section of this website.

The installation of |shape-it| assumes that the :envvar:`BABEL_DATADIR`, 
:envvar:`BABEL_LIBDIR`, and :envvar:`BABEL_INCLUDEDIR` point to the directories 
where **OpenBabel** has been installed::

	> echo $BABEL_INCLUDEDIR
	/usr/local/openbabel/include/openbabel-2.0/
	> echo $BABEL_LIBDIR
	/usr/local/lib/openbabel/2.3.1/
	> echo $BABEL_DATADIR
	/usr/local/openbabel/share/openbabel/2.3.1/


.. note::

	It has been reported that on some systems, like Ubuntu Linux 10, 
	:envvar:`BABEL_INCLUDEDIR` should not include the :file:`openbabel-2.0` subdirectory.
	Hence, for these systems the :envvar:`BABEL_INCLUDEDIR` environment variable should 
	be set to :file:`/usr/local/openbabel/include/`.

First start by downloading |shape-it| from our :ref:`software <software>` section and
un-tar this file into the :file:`/usr/local/src` directory::

	> cd /usr/local/src
	> sudo tar -xvf ~/Downloads/shape-it-1.0.1.tar.gz

Change into this directory and start the building process::

	> cd shape-it-1.0.1
	> sudo mkdir build
	> cd build
	> sudo cmake ..
	> sudo make
	> sudo make install
	
This latter command will install the |shape-it| executable in the :file:`/usr/local/bin/` directory.
If you need to install in a different location, try the following from the build directory::

	> sudo cmake .. -DCMAKE_INSTALL_PREFIX=../

or specify another install directory as required. Finally, check the installation by entering::

	> which shape-it
	/usr/local/bin/shape-it
	> shape-it -h
	...


**********
References
**********

.. [#grant] Grant, J.A.; Gallardo, M.A.; Pickup, B.T. (1996) 'A fast method of molecular shape 
   comparison: a simple application of a Gaussian description of molecular shape',
   *J. Comp. Chem.* **17**, 1653-1666 [`wiley/19961115
   <http://onlinelibrary.wiley.com/doi/10.1002/(SICI)1096-987X(19961115)17:14%3C1653::AID-JCC7%3E3.0.CO;2-K/abstract>`_]

.. [#green] Green, J.; Kahn, S.; Savoi, H.; Sprague, P.; Teig, S. (1994) 'Chemical function queries 
   for 3D database search', *J. Chem. Inf. Comput. Sci.*, **34**, 1297-1308 [`acs/ci00022a012
   <http://pubs.acs.org/doi/abs/10.1021/ci00022a012>`_]

.. [#karney] Karney, C.F.F. (2006) 'Quaternions in molecular modeling', *J. Mol. Graph. Mod.* 
   **25**, 596-604 [`pdf <http://arxiv.org/pdf/physics/0506177.pdf>`_]

.. [#hasan] Hasan A.A.; Hasan M.A. (2003) 'Constrained gradient descent and line search for solving 
   optimization problem with elliptic constraints', In: *Proceedings ICASSP*, pp II.793-796 
   [`ieee/1202486 <http://ieeexplore.ieee.org/xpl/freeabs_all.jsp?arnumber=1202486>`_]

 

****************
Revision history
****************

Version 1.0.1
==============

Added the :option:`-f` (:option:`--format`) option (based on a patch provided by Björn Grüning from the
University of Freiburg).


Version 1.0.0
==============

This is the first official release of |shape-it|. The program is a successor of the program *Piramid*
from Silicos and is branched out of version 1.0.1 of this program.

Additions to the original *Piramid* version include:

* Porting the documentation to ``html`` and including some improvements.


