DEPLOYDIR=/var/www/html
BACKUPDIR=/var/wwwbackup

deploy:
	cp -r $(DEPLOYDIR) $(BACKUPDIR)/
	rm -rf $(DEPLOYDIR)/*
	cp -r build/* $(DEPLOYDIR)

clean:
	echo "Nothing to Clean"
