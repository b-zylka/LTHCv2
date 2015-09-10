# LTHCv2 LabTech Score Web Portal (LTHC)

This is an extension of the original code base redesigned and optimized for better performance and greater metric integrity with the help of Kyle.  Queries have been updated an extensive code overhaul completed.  There's likely many, many files not in use that will be cleaned out over time in the js and css directories.  This will be the intermediate solution while Admiral Spooner is completing the modular EPiCENTER solution.

[Issues](https://github.com/b-zylka/LTHCv2/issues) - Report any issues here.

### Requirements
<ul>
<li>PHP v5 or higher</li>
<li>Apache v2 or higher</li>
<li>LabTech 2013 or higher</li>
<li>MySQL views, tables, events, procedure below</li>
<li>LabTech EDF's included in dbSetup.sql script</li>
</ul>

#### Important!
You need the appropriate table structure in place for data to be returned.  Please run the dbSetup.sql script first to create the required database infrastructure.

#### MySQL & LabTech Additions:
<p>Please contact me with questions/concerns on the SQL below</p>
<ul>
<li>Create four views named "v_plugin_lthc_xxxx"</li>
<li>Create four tables named "plugin_lthc_xxxx"</li>
<li>Create one stored procedure named "sp_plugin_UpdateScores"</li>
<li>Create three scheduled events named "ev_lthcUpdate_xxxx"</li>
<li>Enable global event scheduler (Please edit your my.ini!)</li>
<ul><li>Add "event_schedule=ON" to your my.ini otherwise scores will not be updated via the events on a daily/weekly/monthly schedule</li>
<li>Default location for your my.ini is "%programfiles%\MySQL\my.ini"</li>
</ul>
<li>Three additional EDF's on the Agent Scope tab of Clients</li>
<li>Ten additional EDF's on the Health Scores Web tab of Clients</li>
<li>Eight additional EDF's on the Health Scores Web tab of Computers</li>
<ul><li>Note that all data population is dependant on "Managed IT - Active" EDF being set to enabled</li>
</ul>
</ul>

### Branding and Configuration
<ul>
<li>Update your branding in config/branding.php to configure your company name, logo, links, etc.</li>
<li>Update your database settings in config/dbconnect.php to allow connection to the LabTech database.</li>
<li>Update the EDF's with your team and client specialist information.</li>
</ul>

###  Notes
This software is provided without any warranty of any kind.  Please use at your own discretion and you're highly advised to not make it publicly available.  Consider the code vulnerable as no authentication is provided.  Use at your own risk!


