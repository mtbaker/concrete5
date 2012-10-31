Mt Baker Ski Area Fork of [Concrete5][c5]
-----------------------------------------------------------

Used for block development.


# Installation Instructions for Concrete5 with Baker.

For Development:

1. `vagrant up`
2. Open [33.33.33.100](http://33.33.33.100)
3. admin/admin


For Production:

1. Make sure your config/, packages/ and files/ directories are writable by a web server. These directories are in the root of the archive. This can either be done by making the owner of the directories the web server user, or by making them world writable using chmod 777 (in Linux/OS X.)
2. Create a new MySQL database and a MySQL user account with the following privileges on that database: INSERT, SELECT, UPDATE, DELETE, CREATE, DROP, ALTER
3. Visit your Concrete5 site in your web browser. You should see an installation screen where you can specify your site's name, your base URL, and your database settings, and the rest of the information necessary to install concrete5.
4. Click through and wait for the installation to complete.
5. concrete5 should be installed.

# Documentation

[RTM][docs]

[XML DB Config](http://www.concrete5.org/documentation/how-tos/developers/creating-and-working-with-db-xml-files/)



[docs]: http://concrete5.org/documentation
[c5]: http://www.concrete5.org


