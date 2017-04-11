package uhk.josef.kanvas;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Graphics;
import java.awt.Point;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.event.MouseMotionAdapter;
import java.awt.image.BufferedImage;
import java.util.ArrayList;
import java.util.List;
import javax.swing.ButtonGroup;
import javax.swing.JFrame;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JPanel;
import javax.swing.JRadioButtonMenuItem;
import javax.swing.SwingUtilities;
import uhk.josef.kanvas.rasterization.Edge;
import uhk.josef.kanvas.rasterization.Edges;
import uhk.josef.kanvas.rasterization.PolygonRasterizerScanline;
import uhk.josef.kanvas.rasterization.Seeder;

/**
 * trida pro kresleni na platno, zahrnuje frame, ovládácí prvky a další.
 * 
 * @author PGRF FIM UHK and JOSEF JANDA
 * @version 2015
 */

public class Canvas {

	private final JFrame frame;// deklarace
	private final JPanel panel;
	private final BufferedImage img;
	private int startX = -1, startY = -1;
	private final uhk.josef.kanvas.rasterization.LineRasterizer liner;
	private uhk.josef.kanvas.rasterization.CircleRasterizer circler;
	private final Edges edges;
	private String type;
	private int vysecStep = 0;
	private Point vysecKonec;
	private final List<uhk.josef.kanvas.rasterization.Point> points = new ArrayList<>();
	private uhk.josef.kanvas.rasterization.Point firstClick;
	private int vysecPolomer;
	private boolean mouseLeftDown = false;
	private Point seminko;

	public Canvas(int width, int height) {
		frame = new JFrame();
		type = "Polygon";
		frame.setTitle("UHK FIM PGRF : Canvas");
		frame.setResizable(true);
		frame.setLayout(new BorderLayout());
		frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		edges = new Edges();// inicializace
		JMenuBar jMenuBar = new JMenuBar();
		JMenu jMenu = new JMenu("Výbìr prvku");
		JMenu fill = new JMenu("Vyplò semínkem");
		JMenu fillSl = new JMenu("Vyplò scanline");
		JMenu jMenu1 = new JMenu("Smazat vše");
		jMenuBar.add(jMenu);
		jMenuBar.add(fill);
		jMenuBar.add(fillSl);
		jMenuBar.add(jMenu1);
		JRadioButtonMenuItem rbMenuItem;
		JRadioButtonMenuItem rbMenuItem2;
		JRadioButtonMenuItem rbMenuItem3;
		ButtonGroup group = new ButtonGroup();
		frame.add(jMenuBar, BorderLayout.PAGE_START); // pøidání menu baru do
														// frame(tvorba menu)
		img = new BufferedImage(width, height, BufferedImage.TYPE_INT_RGB);
		liner = new uhk.josef.kanvas.rasterization.LineRasterizerTrivial(img);
		circler = new uhk.josef.kanvas.rasterization.CircleRasterizationTrivial(img);
		panel = new JPanel();
		panel.setPreferredSize(new Dimension(width, height));
		frame.add(panel);
		frame.pack();
		frame.setVisible(true);

		fill.setToolTipText("Kliknutím vyplníte objekt na plátnì(pokud bylo pravým tlaèítkem myši vsazeno semínko).");

		fill.addMouseListener(new MouseAdapter() {// tlacitko pro seminko,
													// listener
			public void mouseClicked(MouseEvent arg0) {
				Seeder seed = new Seeder(img);
				img.setRGB(seminko.x, seminko.y, 0x0);
				seed.seedFill(seminko.x, seminko.y, 0x00CC66, 0x0);
				present();

			}
		});
		fillSl.setToolTipText("Kliknutím vyplníte objekt na plátnì pomocí scanline.");
		fillSl.addMouseListener(new MouseAdapter() {// tlacitko pro
													// scanline,pridani
													// listeneru

			public void mouseClicked(MouseEvent e) {
				PolygonRasterizerScanline polygon = new PolygonRasterizerScanline(img);
				polygon.drawPolygon(points);

				present();

			}
		});

		rbMenuItem = new JRadioButtonMenuItem("Mnohoúhelník");// pojmenování
																// tlaèítka
		rbMenuItem.addActionListener(new ActionListener() {
			@Override
			public void actionPerformed(ActionEvent arg0) {
				if (rbMenuItem.isSelected()) {
					type = "Polygon";
					startX = -1;
					startY = -1;
					edges.smazatVse();
					start();
				}
			}
		});

		jMenu1.addMouseListener(new MouseAdapter() {// tlacitko smazat vse,
													// pridani listeneru

			public void mouseClicked(MouseEvent arg0) {
				startX = -1;
				startY = -1;
				edges.smazatVse();
				vysecStep = 1;
				vysecPolomer = 0;
				points.clear();
				start();
			}
		});
		rbMenuItem2 = new JRadioButtonMenuItem("Kruh");// nastaveni kruhu
		rbMenuItem2.addActionListener(new ActionListener() {

			@Override
			public void actionPerformed(ActionEvent e) {// pridani listeneru
				if (rbMenuItem2.isSelected()) {
					startX = -1;
					startY = -1;
					type = "Circle";
					edges.smazatVse();
					start();
				}
			}
		});

		rbMenuItem3 = new JRadioButtonMenuItem("Výseè");
		rbMenuItem3.addActionListener(new ActionListener() {

			@Override
			public void actionPerformed(ActionEvent e) {
				if (rbMenuItem3.isSelected()) {
					type = "Vysec";
					vysecStep = 1;
					startX = -1;
					startY = -1;
					start();
				}
			}
		});
		rbMenuItem.setSelected(true);
		group.add(rbMenuItem);
		group.add(rbMenuItem2);
		group.add(rbMenuItem3);
		jMenu.add(rbMenuItem);
		jMenu.add(rbMenuItem2);
		jMenu.add(rbMenuItem3);

		panel.addMouseListener(new MouseAdapter() {
			@Override
			public void mousePressed(final MouseEvent e) {
				if (e.getButton() == MouseEvent.BUTTON1) {
					mouseLeftDown = true;
					if (points.size() == 0) {
						if (e.getX() >= 0 && e.getY() >= 0 && e.getY() <= img.getHeight()
								&& e.getX() <= img.getWidth()) {
							points.add(new uhk.josef.kanvas.rasterization.Point(e.getX(), e.getY()));
						}
					}
				}
				if (type.equals("Polygon") || type.equals("Vyplnovani") || type.equals("Circle")
						|| (type.equals("Vysec") && vysecStep == 1)) {

					if (startX == -1) {// zpracovani prvniho kliku
						startX = e.getX();
						startY = e.getY();
					}
					if (e.getButton() == MouseEvent.BUTTON3) {// click right
																// mouse button
						img.setRGB(e.getX(), e.getY(), 0x00FF99);
						seminko = new Point(e.getX(), e.getY());
						present();
					}
				}
			}

			@Override
			public void mouseReleased(MouseEvent e) {
				if (e.getButton() == MouseEvent.BUTTON1) {
					mouseLeftDown = false;
					if (e.getX() >= 0 && e.getY() >= 0 && e.getY() <= img.getHeight() && e.getX() <= img.getWidth()) {
						points.add(new uhk.josef.kanvas.rasterization.Point(e.getX(), e.getY()));
					}
					if (type.equals("Polygon") | type.equals("Vyplnovani")) {
						if (e.getX() >= 0 && e.getY() >= 0 && e.getY() <= img.getHeight()
								&& e.getX() <= img.getWidth()) {
							Edge edge = new Edge(startX, startY, e.getX(), e.getY(), 0x66FF66);
							edges.add(edge);
							startX = e.getX();
							startY = e.getY();
							img.getRGB(e.getX(), e.getY());
						}
					}
					if (type.equals("Circle")) {
						if (vysecStep == 1) {

						}
						vysecStep += 1;
					}
					if (type.equals("Vysec")) {
						if (vysecStep == 1) {
							vysecKonec = new Point(e.getX(), e.getY());
							Edge edge = new Edge(startX, startY, e.getX(), e.getY(), 0xffffff);
							vysecPolomer = edge.getSize();
						}
						vysecStep += 1;
					}
				}
			}
		});
		panel.addMouseMotionListener(new MouseMotionAdapter() {

			@Override
			public void mouseDragged(final MouseEvent e) {// tazeni mysi
				if (mouseLeftDown) {
					clear(0x0);
					if (type.equals("Vysec")) {
						if (vysecStep == 1) {
							liner.drawLine(startX, startY, e.getX(), e.getY(), 0xffffff);
						}
						if (vysecStep == 2) {
							liner.drawLine(startX, startY, vysecKonec.x, vysecKonec.y, 0xffffff);

							circler.drawCirclePart(startX, startY, vysecKonec.x, vysecKonec.y, e.getX(), e.getY(),
									0xffffff);
						}
					}
					if (type.equals("Polygon") | type.equals("Vyplnovani")) {
						if (e.getX() >= 0 && e.getY() >= 0 && e.getY() <= img.getHeight()
								&& e.getX() <= img.getWidth()) {
							liner.drawLine(startX, startY, e.getX(), e.getY(), 0xffffff);// poslední
																							// bod
							if (edges.pocetUsecek() != 0) {
								liner.drawLine(e.getX(), e.getY(), edges.get(0).getX1(), edges.get(0).getY1(),
										0xffffff);// prvníbod
							}
						}
						edges.draw(liner);

					} else {
						if (type.equals("Circle")) {
							// kresleni kruhu
							if (startX == -1) {
								startX = e.getX();
								startY = e.getY();
							} else {

								circler.drawCircle(startX, startY, e.getX(), e.getY(), 0xCC00FF);

								present();
							}
						}
					}
					present();
				}
			}

		});

	}

	public void clear(int color) {
		Graphics gr = img.getGraphics();
		gr.setColor(new Color(color));
		gr.fillRect(0, 0, img.getWidth(), img.getHeight());
	}

	public void present() {
		if (panel.getGraphics() != null)
			panel.getGraphics().drawImage(img, 0, 0, null);
	}

	public void draw() {
		clear(0x0);
		img.setRGB(10, 10, 0xffff00);
	}

	public void start() {
		draw();
		present();
	}

	public static void main(String[] args) {
		Canvas canvas = new Canvas(800, 600);
		SwingUtilities.invokeLater(() -> {
			SwingUtilities.invokeLater(() -> {
				SwingUtilities.invokeLater(() -> {
					SwingUtilities.invokeLater(() -> {
						canvas.start();
					});
				});
			});
		});
	}

}