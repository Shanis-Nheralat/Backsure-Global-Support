/**
 * Backsure Global Support
 * Inquiry Management System - Backend
 * Version: 1.0
 * 
 * This file contains the server-side code for handling inquiries,
 * including database interactions, email notifications, and API endpoints.
 */

// Import required modules
const express = require('express');
const router = express.Router();
const mongoose = require('mongoose');
const multer = require('multer');
const nodemailer = require('nodemailer');
const validator = require('validator');
const bodyParser = require('body-parser');
const cors = require('cors');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcrypt');
const { check, validationResult } = require('express-validator');

// Initialize Express app
const app = express();

// Middleware configuration
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Configure multer for file uploads
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'uploads/');
  },
  filename: (req, file, cb) => {
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    const fileExt = file.originalname.split('.').pop();
    cb(null, `${file.fieldname}-${uniqueSuffix}.${fileExt}`);
  }
});

const fileFilter = (req, file, cb) => {
  // Accept only specific file types
  if (file.mimetype === 'application/pdf' || 
      file.mimetype === 'application/msword' || 
      file.mimetype === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
      file.mimetype === 'image/jpeg' || 
      file.mimetype === 'image/png') {
    cb(null, true);
  } else {
    cb(new Error('Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG files are allowed.'), false);
  }
};

const upload = multer({ 
  storage: storage,
  fileFilter: fileFilter,
  limits: {
    fileSize: 10 * 1024 * 1024 // 10MB size limit
  }
});

// Database connection
mongoose.connect('mongodb://localhost:27017/backsure_inquiries', {
  useNewUrlParser: true,
  useUnifiedTopology: true,
  useFindAndModify: false,
  useCreateIndex: true
}).then(() => {
  console.log('Connected to MongoDB');
}).catch(err => {
  console.error('MongoDB connection error:', err);
});

// Define Mongoose schemas and models
const inquirySchema = new mongoose.Schema({
  name: {
    type: String,
    required: true,
    trim: true
  },
  email: {
    type: String,
    required: true,
    trim: true,
    lowercase: true,
    validate: {
      validator: validator.isEmail,
      message: 'Invalid email format'
    }
  },
  phone: {
    type: String,
    trim: true
  },
  company: {
    type: String,
    trim: true
  },
  subject: {
    type: String,
    required: true,
    trim: true
  },
  message: {
    type: String,
    required: true,
    trim: true
  },
  service: {
    type: String,
    enum: ['Finance & Accounting', 'Insurance Support', 'Dedicated Teams', 'Business Care', 'Other'],
    default: 'Other'
  },
  status: {
    type: String,
    enum: ['New', 'Contacted', 'In Progress', 'Qualified', 'Unqualified', 'Closed'],
    default: 'New'
  },
  source: {
    type: String,
    enum: ['Website', 'Referral', 'Social Media', 'Email Campaign', 'Event', 'Other'],
    default: 'Website'
  },
  priority: {
    type: String,
    enum: ['Low', 'Medium', 'High', 'Urgent'],
    default: 'Medium'
  },
  attachments: [
    {
      filename: String,
      path: String,
      mimetype: String,
      size: Number
    }
  ],
  notes: [
    {
      content: String,
      createdBy: {
        type: mongoose.Schema.Types.ObjectId,
        ref: 'User'
      },
      createdAt: {
        type: Date,
        default: Date.now
      }
    }
  ],
  assignedTo: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'User'
  },
  createdAt: {
    type: Date,
    default: Date.now
  },
  updatedAt: {
    type: Date,
    default: Date.now
  },
  followUpDate: {
    type: Date
  },
  lastContactDate: {
    type: Date
  }
});

// Add index for faster queries
inquirySchema.index({ email: 1 });
inquirySchema.index({ status: 1 });
inquirySchema.index({ createdAt: 1 });
inquirySchema.index({ assignedTo: 1 });

// Pre-save middleware to update the updatedAt field
inquirySchema.pre('save', function(next) {
  this.updatedAt = Date.now();
  next();
});

// User schema for admin users
const userSchema = new mongoose.Schema({
  username: {
    type: String,
    required: true,
    unique: true,
    trim: true
  },
  password: {
    type: String,
    required: true
  },
  email: {
    type: String,
    required: true,
    unique: true,
    trim: true,
    lowercase: true,
    validate: {
      validator: validator.isEmail,
      message: 'Invalid email format'
    }
  },
  fullName: {
    type: String,
    required: true,
    trim: true
  },
  role: {
    type: String,
    enum: ['admin', 'sales', 'support', 'marketing'],
    default: 'support'
  },
  department: {
    type: String,
    enum: ['Finance & Accounting', 'Insurance Support', 'Dedicated Teams', 'Business Care', 'General'],
    default: 'General'
  },
  active: {
    type: Boolean,
    default: true
  },
  lastLogin: {
    type: Date
  },
  createdAt: {
    type: Date,
    default: Date.now
  }
});

// Pre-save middleware to hash password
userSchema.pre('save', async function(next) {
  if (!this.isModified('password')) return next();
  
  try {
    const salt = await bcrypt.genSalt(10);
    this.password = await bcrypt.hash(this.password, salt);
    next();
  } catch (err) {
    next(err);
  }
});

// Method to compare password
userSchema.methods.comparePassword = async function(candidatePassword) {
  return bcrypt.compare(candidatePassword, this.password);
};

// Create models
const Inquiry = mongoose.model('Inquiry', inquirySchema);
const User = mongoose.model('User', userSchema);

// Email configuration
const emailTransporter = nodemailer.createTransport({
  host: process.env.EMAIL_HOST || 'smtp.backsure.com',
  port: process.env.EMAIL_PORT || 587,
  secure: process.env.EMAIL_SECURE === 'true',
  auth: {
    user: process.env.EMAIL_USER || 'notifications@backsure.com',
    pass: process.env.EMAIL_PASS || 'your-password'
  }
});

// Helper function to send email
async function sendEmail(to, subject, html) {
  try {
    const mailOptions = {
      from: '"Backsure Global Support" <notifications@backsure.com>',
      to: to,
      subject: subject,
      html: html
    };
    
    const info = await emailTransporter.sendMail(mailOptions);
    console.log('Email sent:', info.messageId);
    return info;
  } catch (error) {
    console.error('Error sending email:', error);
    throw error;
  }
}

// Authentication middleware
function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];
  
  if (!token) {
    return res.status(401).json({ error: 'Access denied. No token provided.' });
  }
  
  jwt.verify(token, process.env.JWT_SECRET || 'your-secret-key', (err, user) => {
    if (err) {
      return res.status(403).json({ error: 'Invalid or expired token.' });
    }
    
    req.user = user;
    next();
  });
}

// Role-based authorization middleware
function authorize(roles = []) {
  if (typeof roles === 'string') {
    roles = [roles];
  }
  
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).json({ error: 'Unauthorized' });
    }
    
    if (roles.length && !roles.includes(req.user.role)) {
      return res.status(403).json({ error: 'Forbidden: Insufficient permissions' });
    }
    
    next();
  };
}

// API Endpoints

// User login
router.post('/api/login', [
  check('username').notEmpty().withMessage('Username is required'),
  check('password').notEmpty().withMessage('Password is required')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    // Find user by username
    const user = await User.findOne({ username: req.body.username });
    if (!user) {
      return res.status(401).json({ error: 'Invalid username or password' });
    }
    
    // Check if user is active
    if (!user.active) {
      return res.status(401).json({ error: 'Account is disabled. Please contact administrator.' });
    }
    
    // Verify password
    const isMatch = await user.comparePassword(req.body.password);
    if (!isMatch) {
      return res.status(401).json({ error: 'Invalid username or password' });
    }
    
    // Update last login time
    user.lastLogin = Date.now();
    await user.save();
    
    // Generate JWT token
    const token = jwt.sign(
      { 
        id: user._id, 
        username: user.username, 
        role: user.role,
        department: user.department 
      },
      process.env.JWT_SECRET || 'your-secret-key',
      { expiresIn: '12h' }
    );
    
    // Return user data and token
    res.json({
      token,
      user: {
        id: user._id,
        username: user.username,
        email: user.email,
        fullName: user.fullName,
        role: user.role,
        department: user.department
      }
    });
    
  } catch (err) {
    console.error('Login error:', err);
    res.status(500).json({ error: 'Server error during login' });
  }
});

// Public inquiry submission endpoint
router.post('/api/inquiries', [
  upload.array('attachments', 5),
  check('name').notEmpty().withMessage('Name is required'),
  check('email').isEmail().withMessage('Valid email is required'),
  check('subject').notEmpty().withMessage('Subject is required'),
  check('message').notEmpty().withMessage('Message is required')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    // Process file attachments
    const attachments = req.files ? req.files.map(file => ({
      filename: file.originalname,
      path: file.path,
      mimetype: file.mimetype,
      size: file.size
    })) : [];
    
    // Create new inquiry
    const inquiry = new Inquiry({
      name: req.body.name,
      email: req.body.email,
      phone: req.body.phone || '',
      company: req.body.company || '',
      subject: req.body.subject,
      message: req.body.message,
      service: req.body.service || 'Other',
      source: req.body.source || 'Website',
      attachments: attachments
    });
    
    // Save to database
    await inquiry.save();
    
    // Send confirmation email to client
    const clientEmailHtml = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>Thank you for contacting Backsure Global Support</h2>
        <p>Dear ${req.body.name},</p>
        <p>We have received your inquiry regarding "${req.body.subject}".</p>
        <p>Our team will review your message and get back to you as soon as possible, usually within 1-2 business days.</p>
        <p>For your reference, here's a summary of your inquiry:</p>
        <ul>
          <li><strong>Inquiry ID:</strong> ${inquiry._id}</li>
          <li><strong>Subject:</strong> ${req.body.subject}</li>
          <li><strong>Service:</strong> ${req.body.service || 'Other'}</li>
          <li><strong>Date Submitted:</strong> ${new Date().toLocaleString()}</li>
        </ul>
        <p>If you have any additional information to provide, please reply to this email.</p>
        <p>Best regards,<br>Backsure Global Support Team</p>
      </div>
    `;
    
    await sendEmail(req.body.email, 'Your Inquiry Received - Backsure Global Support', clientEmailHtml);
    
    // Send notification to admin/staff
    const notificationEmail = process.env.NOTIFICATION_EMAIL || 'inquiries@backsure.com';
    const staffEmailHtml = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2>New Inquiry Received</h2>
        <p>A new inquiry has been submitted through the website:</p>
        <ul>
          <li><strong>Name:</strong> ${req.body.name}</li>
          <li><strong>Email:</strong> ${req.body.email}</li>
          <li><strong>Phone:</strong> ${req.body.phone || 'Not provided'}</li>
          <li><strong>Company:</strong> ${req.body.company || 'Not provided'}</li>
          <li><strong>Subject:</strong> ${req.body.subject}</li>
          <li><strong>Service:</strong> ${req.body.service || 'Other'}</li>
          <li><strong>Source:</strong> ${req.body.source || 'Website'}</li>
          <li><strong>Attachments:</strong> ${attachments.length} file(s)</li>
        </ul>
        <p><strong>Message:</strong></p>
        <p>${req.body.message}</p>
        <p><a href="${process.env.ADMIN_URL || 'https://admin.backsure.com'}/inquiries/${inquiry._id}">View in Admin Panel</a></p>
      </div>
    `;
    
    await sendEmail(notificationEmail, 'New Inquiry: ' + req.body.subject, staffEmailHtml);
    
    // Return success response
    res.status(201).json({ 
      success: true, 
      message: 'Inquiry submitted successfully', 
      inquiryId: inquiry._id 
    });
    
  } catch (err) {
    console.error('Inquiry submission error:', err);
    res.status(500).json({ error: 'Error submitting inquiry' });
  }
});

// Get all inquiries (admin only)
router.get('/api/inquiries', authenticateToken, authorize(['admin', 'sales']), async (req, res) => {
  try {
    // Parse query parameters
    const page = parseInt(req.query.page) || 1;
    const limit = parseInt(req.query.limit) || 20;
    const skip = (page - 1) * limit;
    
    // Build query based on filters
    let query = {};
    
    if (req.query.status) {
      query.status = req.query.status;
    }
    
    if (req.query.service) {
      query.service = req.query.service;
    }
    
    if (req.query.search) {
      query.$or = [
        { name: { $regex: req.query.search, $options: 'i' } },
        { email: { $regex: req.query.search, $options: 'i' } },
        { company: { $regex: req.query.search, $options: 'i' } },
        { subject: { $regex: req.query.search, $options: 'i' } },
        { message: { $regex: req.query.search, $options: 'i' } }
      ];
    }
    
    // Filter by assigned user (for non-admin roles)
    if (req.user.role !== 'admin' && !req.query.all) {
      query.assignedTo = req.user.id;
    }
    
    // Date range filter
    if (req.query.startDate && req.query.endDate) {
      const startDate = new Date(req.query.startDate);
      const endDate = new Date(req.query.endDate);
      endDate.setHours(23, 59, 59, 999); // Set to end of day
      
      query.createdAt = {
        $gte: startDate,
        $lte: endDate
      };
    }
    
    // Sort options
    const sortOptions = {};
    if (req.query.sortBy) {
      sortOptions[req.query.sortBy] = req.query.sortOrder === 'desc' ? -1 : 1;
    } else {
      sortOptions.createdAt = -1; // Default sort by newest
    }
    
    // Execute query with pagination
    const inquiries = await Inquiry.find(query)
      .populate('assignedTo', 'fullName email')
      .sort(sortOptions)
      .skip(skip)
      .limit(limit);
    
    // Get total count for pagination
    const total = await Inquiry.countDocuments(query);
    
    // Return results
    res.json({
      inquiries,
      pagination: {
        total,
        page,
        limit,
        pages: Math.ceil(total / limit)
      }
    });
    
  } catch (err) {
    console.error('Error fetching inquiries:', err);
    res.status(500).json({ error: 'Server error while fetching inquiries' });
  }
});

// Get single inquiry by ID
router.get('/api/inquiries/:id', authenticateToken, async (req, res) => {
  try {
    const inquiry = await Inquiry.findById(req.params.id)
      .populate('assignedTo', 'fullName email')
      .populate('notes.createdBy', 'fullName');
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Check if user has permission to view this inquiry
    if (req.user.role !== 'admin' && 
        inquiry.assignedTo && 
        inquiry.assignedTo._id.toString() !== req.user.id) {
      return res.status(403).json({ error: 'You do not have permission to view this inquiry' });
    }
    
    res.json(inquiry);
    
  } catch (err) {
    console.error('Error fetching inquiry:', err);
    res.status(500).json({ error: 'Server error while fetching inquiry' });
  }
});

// Update inquiry
router.put('/api/inquiries/:id', authenticateToken, [
  check('status').optional().isIn(['New', 'Contacted', 'In Progress', 'Qualified', 'Unqualified', 'Closed']),
  check('priority').optional().isIn(['Low', 'Medium', 'High', 'Urgent']),
  check('assignedTo').optional().isMongoId()
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    const inquiry = await Inquiry.findById(req.params.id);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Check if user has permission to update this inquiry
    if (req.user.role !== 'admin' && 
        inquiry.assignedTo && 
        inquiry.assignedTo.toString() !== req.user.id) {
      return res.status(403).json({ error: 'You do not have permission to update this inquiry' });
    }
    
    // Fields that can be updated
    const updatableFields = [
      'status', 'priority', 'assignedTo', 'followUpDate'
    ];
    
    // Update fields if provided in request
    updatableFields.forEach(field => {
      if (req.body[field] !== undefined) {
        inquiry[field] = req.body[field];
      }
    });
    
    // Check if status changed to "Contacted"
    if (req.body.status === 'Contacted' && inquiry.status !== 'Contacted') {
      inquiry.lastContactDate = Date.now();
    }
    
    // Save changes
    await inquiry.save();
    
    // If inquiry was assigned to someone, send notification
    if (req.body.assignedTo && 
        (!inquiry.assignedTo || 
         inquiry.assignedTo.toString() !== req.body.assignedTo)) {
      
      const assignedUser = await User.findById(req.body.assignedTo);
      
      if (assignedUser) {
        const assignmentEmailHtml = `
          <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>New Inquiry Assigned to You</h2>
            <p>Dear ${assignedUser.fullName},</p>
            <p>An inquiry has been assigned to you for follow-up:</p>
            <ul>
              <li><strong>Inquiry ID:</strong> ${inquiry._id}</li>
              <li><strong>Client:</strong> ${inquiry.name} (${inquiry.email})</li>
              <li><strong>Subject:</strong> ${inquiry.subject}</li>
              <li><strong>Status:</strong> ${inquiry.status}</li>
              <li><strong>Priority:</strong> ${inquiry.priority}</li>
            </ul>
            <p><a href="${process.env.ADMIN_URL || 'https://admin.backsure.com'}/inquiries/${inquiry._id}">View Inquiry Details</a></p>
            <p>Please review and follow up as soon as possible.</p>
            <p>Best regards,<br>Backsure Global Support</p>
          </div>
        `;
        
        await sendEmail(
          assignedUser.email, 
          `New Inquiry Assigned: ${inquiry.subject}`, 
          assignmentEmailHtml
        );
      }
    }
    
    res.json({ 
      success: true, 
      message: 'Inquiry updated successfully', 
      inquiry 
    });
    
  } catch (err) {
    console.error('Error updating inquiry:', err);
    res.status(500).json({ error: 'Server error while updating inquiry' });
  }
});

// Add note to inquiry
router.post('/api/inquiries/:id/notes', authenticateToken, [
  check('content').notEmpty().withMessage('Note content is required')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    const inquiry = await Inquiry.findById(req.params.id);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Check if user has permission to add note
    if (req.user.role !== 'admin' && 
        inquiry.assignedTo && 
        inquiry.assignedTo.toString() !== req.user.id) {
      return res.status(403).json({ error: 'You do not have permission to add notes to this inquiry' });
    }
    
    // Add new note
    const newNote = {
      content: req.body.content,
      createdBy: req.user.id,
      createdAt: Date.now()
    };
    
    inquiry.notes.push(newNote);
    await inquiry.save();
    
    // Get the populated note to return
    const populatedInquiry = await Inquiry.findById(req.params.id)
      .populate('notes.createdBy', 'fullName');
    
    const addedNote = populatedInquiry.notes[populatedInquiry.notes.length - 1];
    
    res.status(201).json({ 
      success: true, 
      message: 'Note added successfully', 
      note: addedNote 
    });
    
  } catch (err) {
    console.error('Error adding note:', err);
    res.status(500).json({ error: 'Server error while adding note' });
  }
});

// Delete note from inquiry
router.delete('/api/inquiries/:inquiryId/notes/:noteId', authenticateToken, authorize(['admin']), async (req, res) => {
  try {
    const inquiry = await Inquiry.findById(req.params.inquiryId);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Find note index
    const noteIndex = inquiry.notes.findIndex(
      note => note._id.toString() === req.params.noteId
    );
    
    if (noteIndex === -1) {
      return res.status(404).json({ error: 'Note not found' });
    }
    
    // Remove note
    inquiry.notes.splice(noteIndex, 1);
    await inquiry.save();
    
    res.json({ 
      success: true, 
      message: 'Note deleted successfully' 
    });
    
  } catch (err) {
    console.error('Error deleting note:', err);
    res.status(500).json({ error: 'Server error while deleting note' });
  }
});

// Upload additional attachments to inquiry
router.post('/api/inquiries/:id/attachments', authenticateToken, upload.array('attachments', 5), async (req, res) => {
  try {
    const inquiry = await Inquiry.findById(req.params.id);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Check if user has permission
    if (req.user.role !== 'admin' && 
        inquiry.assignedTo && 
        inquiry.assignedTo.toString() !== req.user.id) {
      return res.status(403).json({ error: 'You do not have permission to add attachments to this inquiry' });
    }
    
    // Process file attachments
    const newAttachments = req.files ? req.files.map(file => ({
      filename: file.originalname,
      path: file.path,
      mimetype: file.mimetype,
      size: file.size
    })) : [];
    
    // Add attachments to inquiry
    inquiry.attachments = [...inquiry.attachments, ...newAttachments];
    await inquiry.save();
    
    res.status(201).json({ 
      success: true, 
      message: 'Attachments added successfully', 
      attachments: newAttachments 
    });
    
  } catch (err) {
    console.error('Error adding attachments:', err);
    res.status(500).json({ error: 'Server error while adding attachments' });
  }
});

// Delete attachment from inquiry
router.delete('/api/inquiries/:inquiryId/attachments/:attachmentId', authenticateToken, authorize(['admin']), async (req, res) => {
  try {
    const inquiry = await Inquiry.findById(req.params.inquiryId);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Find attachment index
    const attachmentIndex = inquiry.attachments.findIndex(
      attachment => attachment._id.toString() === req.params.attachmentId
    );
    
    if (attachmentIndex === -1) {
      return res.status(404).json({ error: 'Attachment not found' });
    }
    
    // Get the attachment path for deletion
    const attachmentPath = inquiry.attachments[attachmentIndex].path;
    
    // Remove attachment from inquiry
    inquiry.attachments.splice(attachmentIndex, 1);
    await inquiry.save();
    
    // Delete file from server (use fs.unlink)
    const fs = require('fs');
    fs.unlink(attachmentPath, (err) => {
      if (err) {
        console.error('Error deleting file:', err);
      }
    });
    
    res.json({ 
      success: true, 
      message: 'Attachment deleted successfully' 
    });
    
  } catch (err) {
    console.error('Error deleting attachment:', err);
    res.status(500).json({ error: 'Server error while deleting attachment' });
  }
});

// Get statistics for dashboard
router.get('/api/inquiries/stats/dashboard', authenticateToken, async (req, res) => {
  try {
    const stats = {};
    
    // Total inquiries
    stats.total = await Inquiry.countDocuments();
    
    // New inquiries (last 30 days)
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
    
    stats.newLast30Days = await Inquiry.countDocuments({
      createdAt: { $gte: thirtyDaysAgo }
    });
    
    // Status breakdown
    const statusCounts = await Inquiry.aggregate([
      { $group: { _id: '$status', count: { $sum: 1 } } }
    ]);
    
    stats.statusBreakdown = statusCounts.reduce((acc, item) => {
      acc[item._id] = item.count;
      return acc;
    }, {});
    
    // Service breakdown
    const serviceCounts = await Inquiry.aggregate([
      { $group: { _id: '$service', count: { $sum: 1 } } }
    ]);
    
    stats.serviceBreakdown = serviceCounts.reduce((acc, item) => {
      acc[item._id] = item.count;
      return acc;
    }, {});
    
    // Inquiries by month (last 6 months)
    const sixMonthsAgo = new Date();
    sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
    sixMonthsAgo.setDate(1);
    sixMonthsAgo.setHours(0, 0, 0, 0);
    
    const monthlyInquiries = await Inquiry.aggregate([
      {
        $match: {
          createdAt: { $gte: sixMonthsAgo }
        }
      },
      {
        $group: {
          _id: {
            year: { $year: '$createdAt' },
            month: { $month: '$createdAt' }
          },
          count: { $sum: 1 }
        }
      },
      {
        $sort: {
          '_id.year': 1,
          '_id.month': 1
        }
      }
    ]);
    
    // Format monthly data for chart
    stats.monthlyTrend = monthlyInquiries.map(item => {
      const date = new Date(item._id.year, item._id.month - 1, 1);
      return {
        month: date.toLocaleString('default', { month: 'short' }),
        year: item._id.year,
        count: item.count
      };
    });
    
    // Get assignments for current user (if not admin)
    if (req.user.role !== 'admin') {
      stats.assigned = await Inquiry.countDocuments({
        assignedTo: req.user.id
      });
      
      stats.assignedPending = await Inquiry.countDocuments({
        assignedTo: req.user.id,
        status: { $nin: ['Closed', 'Unqualified'] }
      });
    }
    
    res.json(stats);
    
  } catch (err) {
    console.error('Error fetching statistics:', err);
    res.status(500).json({ error: 'Server error while fetching statistics' });
  }
});

// Send follow-up email to client
router.post('/api/inquiries/:id/follow-up', authenticateToken, [
  check('subject').notEmpty().withMessage('Email subject is required'),
  check('message').notEmpty().withMessage('Email message is required')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    const inquiry = await Inquiry.findById(req.params.id);
    
    if (!inquiry) {
      return res.status(404).json({ error: 'Inquiry not found' });
    }
    
    // Check if user has permission
    if (req.user.role !== 'admin' && 
        inquiry.assignedTo && 
        inquiry.assignedTo.toString() !== req.user.id) {
      return res.status(403).json({ error: 'You do not have permission to send follow-up for this inquiry' });
    }
    
    // Get user information
    const user = await User.findById(req.user.id);
    
    // Create email signature
    const signature = `
      <p style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee;">
        ${user.fullName}<br>
        ${user.department}<br>
        Backsure Global Support<br>
        Email: ${user.email}<br>
        Website: <a href="https://www.backsure.com">www.backsure.com</a>
      </p>
    `;
    
    // Prepare email HTML
    const emailHtml = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        ${req.body.message}
        ${signature}
      </div>
    `;
    
    // Send email
    await sendEmail(
      inquiry.email,
      req.body.subject,
      emailHtml
    );
    
    // Update inquiry with follow-up information
    inquiry.lastContactDate = Date.now();
    
    // Add note about follow-up
    inquiry.notes.push({
      content: `Follow-up email sent. Subject: ${req.body.subject}`,
      createdBy: req.user.id,
      createdAt: Date.now()
    });
    
    await inquiry.save();
    
    res.json({ 
      success: true, 
      message: 'Follow-up email sent successfully' 
    });
    
  } catch (err) {
    console.error('Error sending follow-up email:', err);
    res.status(500).json({ error: 'Server error while sending follow-up email' });
  }
});

// Get user list (admin only)
router.get('/api/users', authenticateToken, authorize(['admin']), async (req, res) => {
  try {
    const users = await User.find({}, '-password');
    res.json(users);
  } catch (err) {
    console.error('Error fetching users:', err);
    res.status(500).json({ error: 'Server error while fetching users' });
  }
});

// Create new user (admin only)
router.post('/api/users', authenticateToken, authorize(['admin']), [
  check('username').notEmpty().withMessage('Username is required'),
  check('password').isLength({ min: 6 }).withMessage('Password must be at least 6 characters'),
  check('email').isEmail().withMessage('Valid email is required'),
  check('fullName').notEmpty().withMessage('Full name is required'),
  check('role').isIn(['admin', 'sales', 'support', 'marketing']).withMessage('Invalid role')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    // Check if username already exists
    const existingUsername = await User.findOne({ username: req.body.username });
    if (existingUsername) {
      return res.status(400).json({ error: 'Username already exists' });
    }
    
    // Check if email already exists
    const existingEmail = await User.findOne({ email: req.body.email });
    if (existingEmail) {
      return res.status(400).json({ error: 'Email already exists' });
    }
    
    // Create new user
    const user = new User({
      username: req.body.username,
      password: req.body.password,
      email: req.body.email,
      fullName: req.body.fullName,
      role: req.body.role,
      department: req.body.department || 'General'
    });
    
    await user.save();
    
    // Remove password from response
    const userResponse = user.toObject();
    delete userResponse.password;
    
    res.status(201).json({
      success: true,
      message: 'User created successfully',
      user: userResponse
    });
    
  } catch (err) {
    console.error('Error creating user:', err);
    res.status(500).json({ error: 'Server error while creating user' });
  }
});

// Update user (admin or self)
router.put('/api/users/:id', authenticateToken, [
  check('email').optional().isEmail().withMessage('Valid email is required'),
  check('fullName').optional().notEmpty().withMessage('Full name is required'),
  check('role').optional().isIn(['admin', 'sales', 'support', 'marketing']).withMessage('Invalid role')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    // Check if user has permission (admin or self)
    if (req.user.role !== 'admin' && req.user.id !== req.params.id) {
      return res.status(403).json({ error: 'You do not have permission to update this user' });
    }
    
    const user = await User.findById(req.params.id);
    
    if (!user) {
      return res.status(404).json({ error: 'User not found' });
    }
    
    // Fields that can be updated
    const updatableFields = [
      'email', 'fullName', 'department', 'active'
    ];
    
    // Role can only be updated by admin
    if (req.user.role === 'admin') {
      updatableFields.push('role');
    }
    
    // Update fields if provided in request
    updatableFields.forEach(field => {
      if (req.body[field] !== undefined) {
        user[field] = req.body[field];
      }
    });
    
    // Update password if provided
    if (req.body.password) {
      user.password = req.body.password;
    }
    
    await user.save();
    
    // Remove password from response
    const userResponse = user.toObject();
    delete userResponse.password;
    
    res.json({
      success: true,
      message: 'User updated successfully',
      user: userResponse
    });
    
  } catch (err) {
    console.error('Error updating user:', err);
    res.status(500).json({ error: 'Server error while updating user' });
  }
});

// Delete user (admin only)
router.delete('/api/users/:id', authenticateToken, authorize(['admin']), async (req, res) => {
  try {
    // Prevent deleting your own account
    if (req.user.id === req.params.id) {
      return res.status(400).json({ error: 'Cannot delete your own account' });
    }
    
    const result = await User.findByIdAndDelete(req.params.id);
    
    if (!result) {
      return res.status(404).json({ error: 'User not found' });
    }
    
    res.json({
      success: true,
      message: 'User deleted successfully'
    });
    
  } catch (err) {
    console.error('Error deleting user:', err);
    res.status(500).json({ error: 'Server error while deleting user' });
  }
});

// Get current user profile
router.get('/api/profile', authenticateToken, async (req, res) => {
  try {
    const user = await User.findById(req.user.id, '-password');
    
    if (!user) {
      return res.status(404).json({ error: 'User not found' });
    }
    
    res.json(user);
    
  } catch (err) {
    console.error('Error fetching profile:', err);
    res.status(500).json({ error: 'Server error while fetching profile' });
  }
});

// Change password
router.post('/api/change-password', authenticateToken, [
  check('currentPassword').notEmpty().withMessage('Current password is required'),
  check('newPassword').isLength({ min: 6 }).withMessage('New password must be at least 6 characters')
], async (req, res) => {
  // Validate request
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    return res.status(400).json({ errors: errors.array() });
  }
  
  try {
    const user = await User.findById(req.user.id);
    
    if (!user) {
      return res.status(404).json({ error: 'User not found' });
    }
    
    // Verify current password
    const isMatch = await user.comparePassword(req.body.currentPassword);
    if (!isMatch) {
      return res.status(401).json({ error: 'Current password is incorrect' });
    }
    
    // Update password
    user.password = req.body.newPassword;
    await user.save();
    
    res.json({
      success: true,
      message: 'Password changed successfully'
    });
    
  } catch (err) {
    console.error('Error changing password:', err);
    res.status(500).json({ error: 'Server error while changing password' });
  }
});

// Register routes
app.use('/', router);

// Error handling middleware
app.use((err, req, res, next) => {
  console.error('Server error:', err);
  
  if (err instanceof multer.MulterError) {
    if (err.code === 'LIMIT_FILE_SIZE') {
      return res.status(400).json({ error: 'File too large. Maximum size is 10MB.' });
    }
    return res.status(400).json({ error: `File upload error: ${err.message}` });
  }
  
  res.status(500).json({ error: 'Internal server error' });
});

// Start server
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});

module.exports = app;