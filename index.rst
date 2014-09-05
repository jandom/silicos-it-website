.. _index:

Welcome
=======

Welcome to Silicos-it!
----------------------


.. raw:: html

	<p><strong>Silicos-it</strong> has extensive chemoinformatics expertise to help you solve various questions in these domains:</p>
	<p>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">hit identification</span>
		<span class="gray">&nbsp;[ docking and pharmacophore modeling ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">high-throughput screening</span>
		<span class="gray">&nbsp;[ outlier detection and post-data analysis ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">compound libraries</span>
		<span class="gray">&nbsp;[ characterisation, clustering, selection and acquisition ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">chemistry-aware custom-made IT-applications</span>
		<span class="gray">&nbsp;[ development and integration ]</span>
	</p>


Open source and sharing
-----------------------

.. raw:: html

	<?php

	require_once "settings.php";
	require_once "mailme.php";

	// Connect to database
	$db_server = mysql_connect(mysql_host(), "silide5_php", "kreeft1963");
	if (!$db_server)
	{
		mailme($_SERVER['PHP_SELF'] . " -> unable to connect to MySQL: " . mysql_error());
		exit(1);
	}
	mysql_select_db("silide5_downloads");
	if (!mysql_select_db("silide5_downloads"))
	{
		mailme($_SERVER['PHP_SELF'] . " -> unable to select database: " . mysql_error());
		exit(1);
	}

	// Query
	$result = mysql_query("SELECT id, title, text, display_count FROM quotes ORDER BY display_count LIMIT 1");
	if (!$result)
	{
		mailme($_SERVER['PHP_SELF'] . " -> database access failed: " . mysql_error());
		exit(1);
	}
	else
	{
		$row = mysql_fetch_row($result);
		$id = $row[0];
		$title = $row[1];
		$text = $row[2];
		$count = $row[3] + 1;
		$query = sprintf("UPDATE quotes SET display_count = '%d' WHERE id = '%s'", $count, $id);
		mysql_query($query);
		echo "<div class=\"quotationbar\">";
		echo "<p class=\"first quotationbar-title\">";
		echo $title;
		echo "</p>";
		echo "<p class=\"last\">";
		echo $text;
		echo "</p>";
		echo "</div>";
	}
	?>


**Silicos-it** contributes its expertise to the chemoinformatics community by porting its source code 
into the open source domain. Famous examples include our `spectrophore 
<http://www.jcheminf.com/content/3/S1/P7>`_ descriptors, the filtering program |filter-it| and 
the pharmacophore tool |align-it|. Information and downloads of all our open source programs are 
found in the :ref:`software <software>` section.

This site has been developed to share information on open source chemoinformatics and how 
it can help you in your daily work. Our :ref:`cookbook <cookbook>` groups together 
hints for working with molecular toolkits, shares known problems and gives insight in our
learning experiences. The :ref:`molspace <molspace>` 
section gives you some background on our large collection of vendor compounds that we maintain 
to support our clients with a variety of chemoinformatics solutions. And finally, our :ref:`links 
<links>` page gives you access to other sites in the chemoinformatics arena that we find 
interesting enough for sharing.


Customers and partners
----------------------

.. raw:: html

	<p>Over many years, <strong>Silicos-it</strong> has build up a very diverse and interesting
	<a class="reference internal" href="about/about.html#about-customers"><em>customer base</em></a>,
	including:</p>
	<p>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">pharmaceuticals</span>
		<span class="gray">&nbsp;[ to identify novel inhibitors against challenging biological targets ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">biotechnology companies</span>
		<span class="gray">&nbsp;[ to find leads using docking and pharmacophore approaches ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">startups</span>
		<span class="gray">&nbsp;[ in need of molecular modeling support for their lead optimization efforts ]</span><br/>
	<span class="green">&nbsp;&nbsp;&nbsp;&#10003;</span>&nbsp;&nbsp;<span class="darkgray">universities</span>
		<span class="gray">&nbsp;[ to setup internal compound database and storage systems ]</span>
	</p>
	
In order to bring to our customers an even wider range of IT-support, we have also
setup extensive partnerships with `Altran <http://www.altran.be/>`_ and `Emweb <http://www.emweb.be/>`_. 
We hope that this website encourages you to :ref:`contact us <about_contact>` to discuss 
potential collaborative work.


.. toctree::
   :hidden:
   :maxdepth: 3

   self
   software/software
   cookbook/cookbook
   molspace/molspace
   links/links
   About <about/about>

