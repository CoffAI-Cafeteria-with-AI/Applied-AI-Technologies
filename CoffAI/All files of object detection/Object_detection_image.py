######## Image Object Detection Using Tensorflow-trained Classifier #########
#
# Author: Evan Juras
# Date: 1/15/18
# Description: 
# This program uses a TensorFlow-trained neural network to perform object detection.
# It loads the classifier and uses it to perform object detection on an image.
# It draws boxes, scores, and labels around the objects of interest in the image.

## Some of the code is copied from Google's example at
## https://github.com/tensorflow/models/blob/master/research/object_detection/object_detection_tutorial.ipynb

## and some is copied from Dat Tran's example at
## https://github.com/datitran/object_detector_app/blob/master/object_detection_app.py

## but I changed it to make it more understandable to me.

# Import packages
import os
import cv2
import numpy as np
import tensorflow as tf
import sys
from datetime import datetime, date
# This is needed since the notebook is stored in the object_detection folder.
sys.path.append("..")

# Import utilites
from utils import label_map_util
from utils import visualization_utils as vis_util
# Name of the directory containing the object detection module we're using
MODEL_NAME = 'inference_graph'
#----------------------------------------------------------------------------
#Code inserted from us
#Configure Paths
BASIC_PATH = 'c:/tensorflow1/models/research/object_detection/'
PATH_UPLOAD = BASIC_PATH + 'uploads'
IMAGE_NAME = PATH_UPLOAD + '/000146.jpg'
PATH_FINISHED = BASIC_PATH + 'finishedPictures'
IMAGE_SAVE = PATH_FINISHED + '/000146.jpg'
IMAGE_BASENAME = os.path.basename(IMAGE_SAVE)
PATH_BILLS = BASIC_PATH + 'bills'
TEXT_FILE = PATH_BILLS + '/bill_counter.txt'
#Check if directories exist if not create them
if not os.path.exists(PATH_UPLOAD):
    os.mkdir(PATH_UPLOAD)
if not os.path.exists(PATH_FINISHED):
    os.mkdir(PATH_FINISHED)
if not os.path.exists(PATH_BILLS):
    os.mkdir(PATH_BILLS)
os.chdir(PATH_BILLS)
#if textFile with Counter doesn't exist set counter 0, create file and write inside
if not os.path.exists(TEXT_FILE):
    counter = 0
    f = open(TEXT_FILE, "w+")
    f.write("%d"%counter)
    f.close()
else:
    fcounter = open(TEXT_FILE, "r")
    if fcounter.mode == 'r':
        counter = int(fcounter.read())
    fcounter.close()
os.chdir(BASIC_PATH)
#------------------------------------------------------------------------------
# Grab path to current working directory
CWD_PATH = os.getcwd()

# Path to frozen detection graph .pb file, which contains the model that is used
# for object detection.
PATH_TO_CKPT = os.path.join(CWD_PATH,MODEL_NAME,'frozen_inference_graph.pb')

# Path to label map file
PATH_TO_LABELS = os.path.join(CWD_PATH,'training','labelmap.pbtxt')

# Path to image
PATH_TO_IMAGE = os.path.join(CWD_PATH,IMAGE_NAME)

# Number of classes the object detector can identify
NUM_CLASSES = 6

# Load the label map.
# Label maps map indices to category names, so that when our convolution
# network predicts `5`, we know that this corresponds to `king`.
# Here we use internal utility functions, but anything that returns a
# dictionary mapping integers to appropriate string labels would be fine
label_map = label_map_util.load_labelmap(PATH_TO_LABELS)
categories = label_map_util.convert_label_map_to_categories(label_map, max_num_classes=NUM_CLASSES, use_display_name=True)
category_index = label_map_util.create_category_index(categories)

# Load the Tensorflow model into memory.
detection_graph = tf.Graph()
with detection_graph.as_default():
    od_graph_def = tf.GraphDef()
    with tf.gfile.GFile(PATH_TO_CKPT, 'rb') as fid:
        serialized_graph = fid.read()
        od_graph_def.ParseFromString(serialized_graph)
        tf.import_graph_def(od_graph_def, name='')

    sess = tf.Session(graph=detection_graph)

# Define input and output tensors (i.e. data) for the object detection classifier

# Input tensor is the image
image_tensor = detection_graph.get_tensor_by_name('image_tensor:0')

# Output tensors are the detection boxes, scores, and classes
# Each box represents a part of the image where a particular object was detected
detection_boxes = detection_graph.get_tensor_by_name('detection_boxes:0')

# Each score represents level of confidence for each of the objects.
# The score is shown on the result image, together with the class label.
detection_scores = detection_graph.get_tensor_by_name('detection_scores:0')
detection_classes = detection_graph.get_tensor_by_name('detection_classes:0')

# Number of objects detected
num_detections = detection_graph.get_tensor_by_name('num_detections:0')

# Load image using OpenCV and
# expand image dimensions to have shape: [1, None, None, 3]
# i.e. a single-column array, where each item in the column has the pixel RGB value
image = cv2.imread(PATH_TO_IMAGE)
image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
image_expanded = np.expand_dims(image_rgb, axis=0)

# Perform the actual detection by running the model with the image as input
(boxes, scores, classes, num) = sess.run(
    [detection_boxes, detection_scores, detection_classes, num_detections],
    feed_dict={image_tensor: image_expanded})
#----------------------------------------------------------
#Code inserted from us
#Create directory with todays date if it doesn't exist
os.chdir(PATH_BILLS)
today = date.today()
today_string = str(today)
dir = PATH_BILLS + "/" + today_string + "/"
filename = "Bill_{0:05d}.txt".format(counter)
if not os.path.exists(dir):
    os.mkdir(dir)
#----------------------------------------------------------


# Draw the results of the detection (aka 'visulaize the results')

vis_util.visualize_boxes_and_labels_on_image_array(
    image,
    np.squeeze(boxes),
    np.squeeze(classes).astype(np.int32),
    np.squeeze(scores),
    category_index,
#--------------------------------------------
#new parameters to be able to write into the bill files
    filepath = dir,
    filename = filename,
    fileBasename = IMAGE_BASENAME,
#------------------------------------------
    use_normalized_coordinates=True,
    line_thickness=8,
    min_score_thresh=0.60)

# All the results have been drawn on image. Now display the image.
#cv2.imshow('Object detector', image)
#-------------------------------------------------
#Don't display the image save it
cv2.imwrite(IMAGE_SAVE, image)
#-------------------------------------------------

#------------------------------------------------
#increment the counter and write into file again
os.chdir(PATH_BILLS)
fcounter = open(TEXT_FILE, "w+")
counter += 1
fcounter.write("%d" %counter)
fcounter.close()
#-------------------------------------------------

#if 

# Press any key to close the image
#cv2.waitKey(0)

# Clean up
cv2.destroyAllWindows()
