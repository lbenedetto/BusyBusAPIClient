import java.io.File;
import java.io.FileWriter;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.List;

public class SQLFileSplitterUtil {
	private static final String[] filenames = new String[]{"shapes", "stops"};

	public static void main(String[] args) throws Exception {
		for (String filename : filenames) {
			int i = 0;
			int fileNum = 0;
			FileWriter out = new FileWriter(new File(filename + fileNum + ".sql"));
			List<String> lines = Files.readAllLines(Paths.get(filename + ".sql"));
			for (String line : lines) {
				if (i == 10000) {
					fileNum++;
					i = 0;
					out.flush();
					out.close();
					out = new FileWriter(new File(filename + fileNum + ".sql"));
				} else {
					out.write(line + "\n");
				}
				i++;
			}
			out.flush();
			out.close();
		}
	}
}
