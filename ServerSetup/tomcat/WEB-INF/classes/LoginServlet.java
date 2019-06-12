import java.io.IOException;
import java.io.PrintWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.*;
import javax.servlet.RequestDispatcher;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.Cookie;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.util.Random;
import java.lang.String;


/**
 * Servlet implementation class LoginServlet
 */
public class LoginServlet extends HttpServlet {
	private static final long serialVersionUID = 1L;
	String databaseURL = "jdbc:mysql://serversetup_mysql_1:3306/Skitter?user=root&password=root&useSSL=false&relaxAutoCommit=true";
    	Connection conn = null;
	Connection conn2 = null;
	protected void doPost(HttpServletRequest request,HttpServletResponse response) throws ServletException, IOException {
		// get request parameters for userID and password
		String user = request.getParameter("user");
		String pwd = request.getParameter("password");
		try {
	        	DriverManager.registerDriver(new com.mysql.jdbc.Driver ());
                	conn = DriverManager.getConnection(databaseURL);
                	if (conn != null) {
          			System.out.println("Connected to the database");
            	    		PreparedStatement sql = conn.prepareStatement("SELECT userid FROM Users WHERE email = ? AND password = ?;");
            	    		sql.setString(1, user);
   				sql.setString(2, pwd);
				System.out.println(sql);
           	    		ResultSet rs = sql.executeQuery();
				if(rs.next()){
					System.out.println("test");
					Random rand = new Random();
					int n = rand.nextInt(100);
					String nRand = String.valueOf(n);
					PreparedStatement set = conn.prepareStatement("UPDATE Users SET sessID = ? WHERE userid= ?;");
					System.out.println(set);
					set.setString(1, nRand);
					set.setInt(2, rs.getInt(1));
					System.out.println(set);
					set.executeUpdate();
					PrintWriter out = response.getWriter();
					String IDS = String.valueOf(rs.getInt(1)) + "," + nRand;
					out.println(IDS);
				}else{
					RequestDispatcher rd = getServletContext().getRequestDispatcher("/login.html");
					PrintWriter out= response.getWriter();
					out.println("<font color=red>Either user name or password is wrong.</font>");
					rd.include(request, response);
					conn.commit();
				}
			}
		}  catch (SQLException ex) {
            		System.out.println("An error occurred. Maybe user/password is invalid");
            		ex.printStackTrace();
			RequestDispatcher rd = getServletContext().getRequestDispatcher("/login.html");
			PrintWriter out= response.getWriter();
			out.println("<font color=red>Either user name or password is wrong.</font>");
			rd.include(request, response);
	        } finally {
            		if (conn != null) {
                		try {
                    			conn.close();
                		} catch (SQLException ex) {
                			ex.printStackTrace();
                		}
            		}
        	}

	}

}
