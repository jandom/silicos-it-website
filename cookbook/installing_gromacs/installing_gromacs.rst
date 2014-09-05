.. _installing_gromacs:


.. highlight:: console

Installing Gromacs
==================

In this section we describe the process that we went through in order to get the molecular 
dynamics program **Gromacs** up and running on Mac **OS X**.

Please join our `Google groups community 
<http://groups.google.com/group/silicos-it-chemoinformatics>`_
to talk about inconsistencies, errors, raise questions or to make suggestions 
for improvement.


FFTW
----

As **Gromacs** is relying on the FFTW C subroutine library for computing discrete Fourier transforms, we 
need to have these installed. Download `FFTW 3.1.1 <http://www.fftw.org/fftw-3.3.1.tar.gz>`_ and save
into the :file:`~/Downloads/` directory. Then unpack into the :file:`/usr/local/src` directory
and install::

    > cd /usr/local/src
    > tar -xvf ~/Downloads/fftw-3.3.1.tar.gz
    > cd fftw-3.3.1
    > ./configure --enable-threads --enable-float
    > make
    > sudo make install

This latter command asks for a root password that you should provide. This installation process installs
the FFTW header files in :file:`/usr/local/include` and the library files in :file:`/usr/local/lib`. 

CMake
-----

Download `cmake 2.8.6 <http://www.cmake.org/files/v2.8/cmake-2.8.6-Darwin64-universal.dmg>`_ 
and follow the installation instructions (make sure to install the command-line links). 
Check the installation::

	> which cmake
	/usr/bin/cmake
	> cmake --version
	cmake version 2.8.6
	
If you have **HomeBrew** installed, you could try this instead::

	> brew install cmake
	

Gromacs
-------

Download and unpack `gromacs 4.5.5 <ftp://ftp.gromacs.org/pub/gromacs/gromacs-4.5.5.tar.gz>`_.
In our case, we have downloaded into the :file:`~/Downloads` directory and are unpacking into 
the :file:`/usr/local/src/` directory::

	> cd /usr/local/src
	> tar -xvf ~/Downloads/gromacs-4.5.5.tar.gz
	> cd gromacs-4.5.5
	> mkdir build
	> cd build
	> cmake ..
	> make
	> sudo make install
	
.. highlight:: none

The latter command installs all **Gromacs**-related files in their respective directories::

> HTML-documentation:             /usr/local/gromacs/share/gromacs/html
> Topology and forcefield files:  /usr/local/gromacs/share/gromacs/top
> Include files:                  /usr/local/gromacs/include/gromacs
> Libraries:                      /usr/local/gromacs/lib
> Binaries:                       /usr/local/gromacs/bin

.. highlight:: console

The last thing to do is to set the paths correctly. This is done by appending the following
command to your :file:`~/.bash_profile` file::

	> echo "source /usr/local/gromacs/bin/GMXRC" >> ~/.bash_profile

In a new shell window, you can now test the installation::

	> mdrun -version
	                         :-)  G  R  O  M  A  C  S  (-:

	               Giving Russians Opium May Alter Current Situation

	                            :-)  VERSION 4.5.5  (-:

	        Written by Emile Apol, Rossen Apostolov, Herman J.C. Berendsen,
	      Aldert van Buuren, Pär Bjelkmar, Rudi van Drunen, Anton Feenstra, 
	        Gerrit Groenhof, Peter Kasson, Per Larsson, Pieter Meulenhoff, 
	           Teemu Murtola, Szilard Pall, Sander Pronk, Roland Schulz, 
	                Michael Shirts, Alfons Sijbers, Peter Tieleman,

	               Berk Hess, David van der Spoel, and Erik Lindahl.

	       Copyright (c) 1991-2000, University of Groningen, The Netherlands.
	            Copyright (c) 2001-2010, The GROMACS development team at
	        Uppsala University & The Royal Institute of Technology, Sweden.
	            check out http://www.gromacs.org for more information.

	         This program is free software; you can redistribute it and/or
	          modify it under the terms of the GNU General Public License
	         as published by the Free Software Foundation; either version 2
	             of the License, or (at your option) any later version.

	                                :-)  mdrun  (-:

	Program: mdrun
	Version:          VERSION 4.5.5
	Precision:        single
	Parallellization: thread_mpi
	FFT Library:      fftw3
