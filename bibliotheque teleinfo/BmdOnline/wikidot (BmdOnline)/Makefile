# build helloworld executable when user executes "make"

teleinfo: teleinfo.o
	$(CC) $(LDFLAGS) -lmysqlclient teleinfo.o -o teleinfo

teleinfo.o: teleinfo.c
	$(CC) $(CFLAGS) -c teleinfo.c

teleinfo_pico: teleinfo_pico.o
	$(CC) $(LDFLAGS) -lmysqlclient teleinfo_pico.o -o teleinfo_pico

teleinfo_pico.o: teleinfo_pico.c
	$(CC) $(CFLAGS) -c teleinfo_pico.c

# remove object files and executable when user executes "make clean"
clean:
	rm *.o teleinfo teleinfo_pico
