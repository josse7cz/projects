package rendering;

import java.util.List;

import transforms.Mat4;
/**
 * prubezna uloha cislo 1
 * 
 * @author Josef Janda
 * @version 9.4.2017
 */
public interface Renderer<V> {
	enum PrimitiveType {
		LINE_LIST, TRIANGLE_LIST
	};

	void render(List<V> vertices, List<Integer> indices, int startIndex, int indexCount, PrimitiveType type, Mat4 mat,
			List<Integer> colors);
}
