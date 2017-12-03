

import javafx.application.Application;
import javafx.embed.swing.SwingFXUtils;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.SnapshotParameters;
import javafx.scene.canvas.Canvas;
import javafx.scene.canvas.GraphicsContext;
import javafx.scene.effect.BlendMode;
import javafx.scene.image.WritableImage;
import javafx.scene.layout.BorderPane;
import javafx.scene.paint.Color;
import javafx.scene.shape.FillRule;
import javafx.scene.text.Font;
import javafx.scene.text.TextAlignment;
import javafx.stage.FileChooser;
import javafx.stage.Stage;

import javax.imageio.ImageIO;
import java.awt.image.RenderedImage;
import java.io.File;
import java.io.IOException;
import java.util.logging.Level;
import java.util.logging.Logger;

public class IconGenerator extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {

        String busNums = "1\n" +
                "2\n" +
                "20\n" +
                "21\n" +
                "22\n" +
                "23\n" +
                "24\n" +
                "25\n" +
                "26\n" +
                "27\n" +
                "28\n" +
                "29\n" +
                "32\n" +
                "33\n" +
                "34\n" +
                "39\n" +
                "42\n" +
                "43\n" +
                "44\n" +
                "45\n" +
                "60\n" +
                "61\n" +
                "62\n" +
                "66\n" +
                "68\n" +
                "74\n" +
                "90\n" +
                "94\n" +
                "95\n" +
                "96\n" +
                "97\n" +
                "98\n" +
                "124\n" +
                "165\n" +
                "172\n" +
                "173";
        String[] numArray = busNums.split("\n");
        int[] nums = new int[numArray.length];
        for (int i = 0; i < nums.length; i++) {
            nums[i] = Integer.parseInt(numArray[i]);
        }
        Canvas arrows = new Canvas(160, 128);
        GraphicsContext arGc = arrows.getGraphicsContext2D();
        for (int i = 0; i < 8; i++) {

            arGc.save();
            arGc.clearRect(0, 0, 160, 128);
            arGc.setFill(Color.TRANSPARENT);
            arGc.fillRect(0, 0, 160, 128);
            double scaleX = arrows.getWidth() / 160;
            double scaleY = arrows.getHeight() / 128;
            arGc.scale(scaleX, scaleY);
            SnapshotParameters sp = new SnapshotParameters();
            sp.setFill(Color.TRANSPARENT);
            double widthThing = arrows.getWidth() - 8;
            double heightThing = arrows.getHeight() - 8;
            switch (i) {

                case 0:
                    arGc.translate(4, 4);
                    break;

                case 1:
                    arGc.translate(widthThing/2 + 4, 4);
                    arGc.rotate(45);
                    break;

                case 2:
                    arGc.translate(widthThing + 4, 4);
                    arGc.rotate(45 * i);
                    break;

                case 3:
                    arGc.translate(widthThing + 4, heightThing/2 + 4);
                    arGc.rotate(45 * i);
                    break;

                case 4:
                    arGc.translate(widthThing + 4, heightThing + 4);
                    arGc.rotate(45 * i);
                    break;

                case 5:
                    arGc.translate(widthThing/2 + 4, heightThing + 4);
                    arGc.rotate(45 * i);
                    break;

                case 6:
                    arGc.translate(4, heightThing + 4);
                    arGc.rotate(45 * i);
                    break;

                case 7:
                    arGc.translate(4, heightThing/2 + 4);
                    arGc.rotate(45 * i);
                    break;

            }
            arGc.setFill(Color.BLACK);
            double[] xs = new double[]{0, 24, 0};
            double[] ys = new double[]{0, 0, 24};
            arGc.fillPolygon(xs, ys, 3);
            //arGc.strokeLine(0, 0, 20, 0);
            //arGc.strokeLine(0, 0, 0, 20);
            //arGc.strokeLine(0, 20, 20, 0);
            arGc.restore();
            WritableImage writableImage = new WritableImage(160, 128);
            arrows.snapshot(sp, writableImage);
            RenderedImage renderedImage = SwingFXUtils.fromFXImage(writableImage, null);
            ImageIO.write(renderedImage, "png", new File("direction" + getBearing(i) + ".png"));

        }
        String colors = getColors(nums.length);
        String[] RGBs = colors.split("/");
        Canvas canvas = new Canvas(160, 128);
        GraphicsContext gc = canvas.getGraphicsContext2D();
        SnapshotParameters sp = new SnapshotParameters();
        sp.setFill(Color.TRANSPARENT);
        for (int i = 0; i < RGBs.length; i++) {
            try {

                gc.setFill(Color.TRANSPARENT);
                gc.fillRect(0, 0, 160, 128);
                String[] rgb = RGBs[i].split(",");
                int r = (int) Double.parseDouble(rgb[0]);
                int g = (int) Double.parseDouble(rgb[1]);
                int b = (int) Double.parseDouble(rgb[2]);
                gc.setFill(Color.rgb(r, g, b));
                gc.fillRoundRect(0, 0, 160, 128, 36, 36);
                gc.setFill(Color.BLACK);
                gc.setFont(Font.font(72));
                gc.setTextAlign(TextAlignment.CENTER);
                gc.fillText(numArray[i], 80, 90);
                WritableImage writableImage = new WritableImage(160, 128);
                canvas.snapshot(sp, writableImage);
                RenderedImage renderedImage = SwingFXUtils.fromFXImage(writableImage, null);
                ImageIO.write(renderedImage, "png", new File(numArray[i] + ".png"));
                gc.clearRect(0, 0, 160, 128);
                gc.setStroke(Color.rgb(r, g, b));
                gc.setLineWidth(4);
                gc.strokeRoundRect(0, 0, 160, 128, 36, 36);
                gc.setFill(Color.BLACK);
                gc.setFont(Font.font(72));
                gc.setTextAlign(TextAlignment.CENTER);
                gc.fillText(numArray[i], 80, 90);
                writableImage = new WritableImage(160, 128);
                canvas.snapshot(sp, writableImage);
                renderedImage = SwingFXUtils.fromFXImage(writableImage, null);
                ImageIO.write(renderedImage, "png", new File(numArray[i] + "unchecked.png"));

            } catch (IOException ex) {
                ex.printStackTrace();
            }
        }
        BorderPane root = new BorderPane();
        primaryStage.setTitle("Hello World");
        primaryStage.setScene(new Scene(root, 300, 275));
        primaryStage.show();

    }

    private String getBearing(int i) {

        String s = "";
        switch (i) {

            case 0:
                s = "315";
                break;
            case 1:
                s = "0";
                break;
            case 2:
                s = "45";
                break;
            case 3:
                s = "90";
                break;
            case 4:
                s = "135";
                break;
            case 5:
                s = "180";
                break;
            case 6:
                s = "225";
                break;
            case 7:
                s = "270";
                break;

        }
        return s;

    }

    private String getColors(int length) {
        int phase = 0;
        int center = 128;
        int width = 127;
        double frequency = Math.PI * 2 / length;
        StringBuilder colors = new StringBuilder();
        for (int i = 0; i < length; i++) {
            double red = Math.sin(frequency * i + 2 + phase) * width + center;
            double green = Math.sin(frequency * i + phase) * width + center;
            double blue = Math.sin(frequency * i + 4 + phase) * width + center;
            colors.append(red).append(",").append(green).append(",").append(blue).append("/");
        }
        return colors.toString();
    }


    public static void main(String[] args) {
        launch(args);
    }
}
