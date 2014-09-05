# Makefile for Sphinx documentation
#

# You can set these variables from the command line.
SPHINXBUILD   			= sphinx-build
BUILDDIR      			= _build

# Internal variables
ALLSPHINXOPTS   		= -d $(BUILDDIR)/doctrees $(PAPEROPT_$(PAPER)) $(SPHINXOPTS) .

# Filter-it variables
FILTER_IT_DIR			= /usr/local/bin/
FILTER_IT				= filter-it

# Strip-it variables
STRIP_IT_DIR			= /usr/local/bin/
STRIP_IT				= strip-it

# Align-it variables
# ...none required

# Shape-it variables
SHAPE_IT_DIR			= /usr/local/bin/
SHAPE_IT				= shape-it

# Som-it variables
# ...none required

# Biscu-it variables
# ...none required




.PHONY: help clean publish publish_clean html dirhtml singlehtml pickle json htmlhelp qthelp devhelp epub latex latexpdf text man changes linkcheck doctest gettext

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  html       	to make standalone HTML files"
	@echo "  publish    	to make everything online (www.silicos-it.com)"
	@echo "  publish_clean	to make everything online after first removing all files online (www.silicos-it.com)"
	@echo "  html       	to make standalone HTML files"
	@echo "  dirhtml    	to make HTML files named index.html in directories"
	@echo "  singlehtml 	to make a single large HTML file"
	@echo "  pickle     	to make pickle files"
	@echo "  json       	to make JSON files"
	@echo "  htmlhelp   	to make HTML files and a HTML help project"
	@echo "  qthelp     	to make HTML files and a qthelp project"
	@echo "  devhelp    	to make HTML files and a Devhelp project"
	@echo "  epub       	to make an epub"
	@echo "  latex      	to make LaTeX files, you can set PAPER=a4 or PAPER=letter"
	@echo "  latexpdf   	to make LaTeX files and run them through pdflatex"
	@echo "  text       	to make text files"
	@echo "  man        	to make manual pages"
	@echo "  texinfo    	to make Texinfo files"
	@echo "  info       	to make Texinfo files and run them through makeinfo"
	@echo "  gettext    	to make PO message catalogs"
	@echo "  changes    	to make an overview of all changed/added/deprecated items"
	@echo "  linkcheck  	to check all external links for integrity"
	@echo "  doctest    	to run all doctests embedded in the documentation (if enabled)"

clean:
	-rm -rf $(BUILDDIR)/*

publish:
	make html
	/usr/bin/ncftpput -u silide5 -p xxxx -S .tmp -R silicos-it.com public_html $(SILICOS_IT_ROOT)/Repo/Web/www.silicos-it.com/_build/html/*
	/usr/bin/ncftpput -u silide5 -p xxxx -S .tmp -R silicos-it.com public_html $(SILICOS_IT_ROOT)/Repo/Web/www.silicos-it.com/_server_data/php.ini
	/usr/bin/ncftpput -u silide5 -p xxxx -S .tmp -R silicos-it.com public_html $(SILICOS_IT_ROOT)/Repo/Web/www.silicos-it.com/_server_data/.htaccess	
	@echo "Upload to www.silicos-it.com finished."

publish_clean:
	/usr/bin/ssh silide5@silicos-it.com rm -rf /home/silide5/public_html/*
	make publish
	@echo "Clean upload to www.silicos-it.com finished."

html:
ifndef SILICOS_IT_ROOT
	@echo "Please create a SILICOS_IT_ROOT environment variable that points to the silicos-it root directory"
endif
	make clean
	@echo
	@echo "Updating Filter-it:"
	echo "> $(FILTER_IT)" > _static/filter-it.txt
	$(FILTER_IT_DIR)$(FILTER_IT) -h 2>> _static/filter-it.txt
	@echo
	@echo "Updating Strip-it:"
	echo "> $(STRIP_IT) -h" > _static/strip-it.txt
	$(STRIP_IT_DIR)$(STRIP_IT) -h 2>> _static/strip-it.txt
	@echo
	@echo "Updating Align-it:"
	@echo "...nothing to be done"
	@echo
	@echo "Updating Shape-it:"
	echo "> $(SHAPE_IT) -h" > _static/shape-it.txt
	$(SHAPE_IT_DIR)$(SHAPE_IT) -h 2>> _static/shape-it.txt
	@echo
	@echo "Updating Biscu-it:"
	@echo "...nothing to be done"
	@echo
	@echo "Updating Som-it:"
	@echo "...nothing to be done"
	@echo
	@echo "Updating www.silicos-it.com:"
	$(SPHINXBUILD) -b html $(ALLSPHINXOPTS) $(BUILDDIR)/html
	/bin/cp -f $(BUILDDIR)/../_images/* $(BUILDDIR)/html/_static
	/bin/cp -f $(BUILDDIR)/../_downloads/* $(BUILDDIR)/html/_downloads
	if [ ! -d $(BUILDDIR)/html/_php ]; then mkdir -p $(BUILDDIR)/html/_php; fi
	/bin/cp -f $(BUILDDIR)/../_php/* $(BUILDDIR)/html/_php
	@echo
	@echo "Build finished. The HTML pages are in $(BUILDDIR)/html."

dirhtml:
	$(SPHINXBUILD) -b dirhtml $(ALLSPHINXOPTS) $(BUILDDIR)/dirhtml
	@echo
	@echo "Build finished. The HTML pages are in $(BUILDDIR)/dirhtml."

singlehtml:
	$(SPHINXBUILD) -b singlehtml $(ALLSPHINXOPTS) $(BUILDDIR)/singlehtml
	@echo
	@echo "Build finished. The HTML page is in $(BUILDDIR)/singlehtml."

pickle:
	$(SPHINXBUILD) -b pickle $(ALLSPHINXOPTS) $(BUILDDIR)/pickle
	@echo
	@echo "Build finished; now you can process the pickle files."

json:
	$(SPHINXBUILD) -b json $(ALLSPHINXOPTS) $(BUILDDIR)/json
	@echo
	@echo "Build finished; now you can process the JSON files."

htmlhelp:
	$(SPHINXBUILD) -b htmlhelp $(ALLSPHINXOPTS) $(BUILDDIR)/htmlhelp
	@echo
	@echo "Build finished; now you can run HTML Help Workshop with the" \
	      ".hhp project file in $(BUILDDIR)/htmlhelp."

qthelp:
	$(SPHINXBUILD) -b qthelp $(ALLSPHINXOPTS) $(BUILDDIR)/qthelp
	@echo
	@echo "Build finished; now you can run "qcollectiongenerator" with the" \
	      ".qhcp project file in $(BUILDDIR)/qthelp, like this:"
	@echo "# qcollectiongenerator $(BUILDDIR)/qthelp/Building_RDKit_On_OSX_10_7.qhcp"
	@echo "To view the help file:"
	@echo "# assistant -collectionFile $(BUILDDIR)/qthelp/Building_RDKit_On_OSX_10_7.qhc"

devhelp:
	$(SPHINXBUILD) -b devhelp $(ALLSPHINXOPTS) $(BUILDDIR)/devhelp
	@echo
	@echo "Build finished."
	@echo "To view the help file:"
	@echo "# mkdir -p $$HOME/.local/share/devhelp/Building_RDKit_On_OSX_10_7"
	@echo "# ln -s $(BUILDDIR)/devhelp $$HOME/.local/share/devhelp/Building_RDKit_On_OSX_10_7"
	@echo "# devhelp"

epub:
	$(SPHINXBUILD) -b epub $(ALLSPHINXOPTS) $(BUILDDIR)/epub
	@echo
	@echo "Build finished. The epub file is in $(BUILDDIR)/epub."

latex:
	$(SPHINXBUILD) -b latex $(ALLSPHINXOPTS) $(BUILDDIR)/latex
	@echo
	@echo "Build finished; the LaTeX files are in $(BUILDDIR)/latex."
	@echo "Run \`make' in that directory to run these through (pdf)latex" \
	      "(use \`make latexpdf' here to do that automatically)."

latexpdf:
	$(SPHINXBUILD) -b latex $(ALLSPHINXOPTS) $(BUILDDIR)/latex
	@echo "Running LaTeX files through pdflatex..."
	$(MAKE) -C $(BUILDDIR)/latex all-pdf
	@echo "pdflatex finished; the PDF files are in $(BUILDDIR)/latex."

text:
	$(SPHINXBUILD) -b text $(ALLSPHINXOPTS) $(BUILDDIR)/text
	@echo
	@echo "Build finished. The text files are in $(BUILDDIR)/text."

man:
	$(SPHINXBUILD) -b man $(ALLSPHINXOPTS) $(BUILDDIR)/man
	@echo
	@echo "Build finished. The manual pages are in $(BUILDDIR)/man."

texinfo:
	$(SPHINXBUILD) -b texinfo $(ALLSPHINXOPTS) $(BUILDDIR)/texinfo
	@echo
	@echo "Build finished. The Texinfo files are in $(BUILDDIR)/texinfo."
	@echo "Run \`make' in that directory to run these through makeinfo" \
	      "(use \`make info' here to do that automatically)."

info:
	$(SPHINXBUILD) -b texinfo $(ALLSPHINXOPTS) $(BUILDDIR)/texinfo
	@echo "Running Texinfo files through makeinfo..."
	make -C $(BUILDDIR)/texinfo info
	@echo "makeinfo finished; the Info files are in $(BUILDDIR)/texinfo."

gettext:
	$(SPHINXBUILD) -b gettext $(I18NSPHINXOPTS) $(BUILDDIR)/locale
	@echo
	@echo "Build finished. The message catalogs are in $(BUILDDIR)/locale."

changes:
	$(SPHINXBUILD) -b changes $(ALLSPHINXOPTS) $(BUILDDIR)/changes
	@echo
	@echo "The overview file is in $(BUILDDIR)/changes."

linkcheck:
	$(SPHINXBUILD) -b linkcheck $(ALLSPHINXOPTS) $(BUILDDIR)/linkcheck
	@echo
	@echo "Link check complete; look for any errors in the above output " \
	      "or in $(BUILDDIR)/linkcheck/output.txt."

doctest:
	$(SPHINXBUILD) -b doctest $(ALLSPHINXOPTS) $(BUILDDIR)/doctest
	@echo "Testing of doctests in the sources finished, look at the " \
	      "results in $(BUILDDIR)/doctest/output.txt."
