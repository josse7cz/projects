package solids;

import java.awt.Color;
import java.util.List;

import transforms.Point3D;

public class Cube extends SolidPoint3D {

	public Cube() {
		for (int i = 0; i <= 100; i++) {
				int r = (int) (Math.random() * 255);
				int g = (int) (Math.random() * 255);
				int b = (int) (Math.random() * 255);
				colors.add(Math.abs(new Color(r, g, b).getRGB()));
				colors.add(Math.abs(new Color(r, g, b).getRGB()));
				
			}
	}
		public Cube(boolean setLiner) {
			for (int i = 0; i <= 100; i++) {
				int r = (int) (Math.random() * 255);
				int g = (int) (Math.random() * 255);
				int b = (int) (Math.random() * 255);
				colors.add(Math.abs(new Color(r, g, b).getRGB()));
				colors.add(Math.abs(new Color(r, g, b).getRGB()));
				
			}
			if (setLiner==true) {
	
				
		
			vertices.add(new Point3D(0, 0, 0));
			vertices.add(new Point3D(1, 0, 0));
			vertices.add(new Point3D(1, 1, 0));
			vertices.add(new Point3D(0, 1, 0));
			vertices.add(new Point3D(0, 0, 1));
			vertices.add(new Point3D(1, 0, 1));
			vertices.add(new Point3D(1, 1, 1));
			vertices.add(new Point3D(0, 1, 1));

			for (int i = 0; i < 4; i++) {
				indices.add(i);
				indices.add((i + 1) % 4);
				indices.add(i);
				indices.add(i + 4);
				indices.add(i + 4);
				indices.add((i + 1) % 4 + 4);
			}

			// Random color

			// indices.add(0);
			// indices.add(1);// 1
			//
			// indices.add(1);
			// indices.add(2);
			//
			// indices.add(2);// 2
			// indices.add(3);
			//
			// indices.add(3);// 3
			// indices.add(0);
			//
			// indices.add(0);
			// indices.add(4);// 4
			//
			// indices.add(4);// 5
			// indices.add(5);
			//
			// indices.add(5);// 6
			// indices.add(1);
			//
			// indices.add(5);
			// indices.add(6);// 7
			//
			// indices.add(6);
			// indices.add(2);// 8
			//
			// indices.add(7);// 9
			// indices.add(6);
			//
			// indices.add(7);// 10
			// indices.add(3);
			//
			// indices.add(7);
			// indices.add(4);// 11

		} else {
			
			vertices.add(new Point3D(0, 1, 0));
			vertices.add(new Point3D(1, 1, 0));
			vertices.add(new Point3D(1, 0, 0));
			vertices.add(new Point3D(0, 0, 0));

			vertices.add(new Point3D(0, 1, 1));
			vertices.add(new Point3D(1, 1, 1));
			vertices.add(new Point3D(1, 0, 1));
			vertices.add(new Point3D(0, 0, 1));

			indices.add(0);
			indices.add(1);// 1
			indices.add(2);

			indices.add(2);
			indices.add(3);// 2
			indices.add(0);

			indices.add(0);
			indices.add(4);// 3
			indices.add(7);

			indices.add(7);
			indices.add(3);// 4
			indices.add(0);

			indices.add(0);
			indices.add(1);// 5
			indices.add(5);

			indices.add(5);
			indices.add(4);// 6
			indices.add(0);

			indices.add(4);
			indices.add(7);// 7
			indices.add(6);

			indices.add(6);
			indices.add(5);// 8
			indices.add(4);

			indices.add(6);
			indices.add(7);// 9
			indices.add(3);

			indices.add(3);
			indices.add(2);// 10
			indices.add(6);

			indices.add(6);
			indices.add(5);// 11
			indices.add(1);

			indices.add(1);
			indices.add(2);// 12
			indices.add(6);
			
		}
//		for (int i = 0; i <= 12; i++) {
//			int r = (int) (Math.random() * 255);
//			int g = (int) (Math.random() * 255);
//			int b = (int) (Math.random() * 255);
//			colors.add(new Color(r, g, b).getRGB());
//			colors.add(new Color(r, g, b).getRGB());
//		}
			
	}

	
	public List<Integer> colors() {

		return colors;
	}

}
