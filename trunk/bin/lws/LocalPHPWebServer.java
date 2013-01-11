/*
 *  Copyright (c) 2009-2010, Cyril Briquet
 * 
 *  This file is part of local-php-web-server software.
 *
 *  local-php-web-server is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  local-php-web-server is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with local-php-web-server.  If not, see <http://www.gnu.org/licenses/>.
 */

// PHPServer.java

import org.mortbay.jetty.Connector;
import org.mortbay.jetty.Server;
import org.mortbay.jetty.nio.SelectChannelConnector;
import org.mortbay.jetty.webapp.WebAppContext;

/**
 * @author Cyril Briquet
 */
public class LocalPHPWebServer {
	// Interface to listen on to
	private static final String HOST="127.0.0.1";
	// port
	private static final int PORT=11250;
	

	public synchronized void serve(String host, int port,String contents_dir) throws Exception {

		final Server server = new Server();

		final Connector connector = new SelectChannelConnector();
		connector.setPort(port);
		connector.setHost(host);
		server.setConnectors(new Connector[] { connector });
    
		final WebAppContext webapp = new WebAppContext();
		webapp.setContextPath("/");
		webapp.setWar(contents_dir);
		webapp.setDefaultsDescriptor("web.xml");
		server.setHandler(webapp);
    
		server.start();

	}

	// ****************************************************************

    public static void main(String[] args) throws Exception {

	System.out.println("This program comes with ABSOLUTELY NO WARRANTY.");
	System.out.println("This is free software, and you are welcome to redistribute it"); 
	System.out.println("under certain conditions; see COPYING for details.");
	System.out.println();

	if (args.length < 1) {
		System.out.println("Usage: java -cp ... PHPServer <website-base-dir>");
	}
	else {
		new LocalPHPWebServer().serve(HOST, PORT, args[0]);
	}
    	
    }

}

