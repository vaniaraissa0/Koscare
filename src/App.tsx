/**
 * KosCare — Sistem Pelaporan & Pemantauan Kerusakan Fasilitas Kos
 * Desain: Aesthetic, Bersih, Cerah & Profesional
 * Dukungan Penuh Tema Dinamis (Pastel Lilac, Baby Blue, Pastel Pink, Sage Green, Cream, Custom)
 * Tipografi Global Fleksibel (Times New Roman, Georgia, Garamond, Arial, Verdana, Trebuchet MS)
 * Responsif Optimal di Tablet & Ponsel (Header, Menu, Tabel, Formulir)
 */

import React, { useState } from 'react';
import {
  House,
  ShieldCheck,
  User,
  LogOut,
  PlusCircle,
  Clock,
  CheckCircle2,
  AlertCircle,
  FileText,
  Search,
  ArrowLeft,
  KeyRound,
  Check,
  Palette,
  RotateCcw,
  Upload,
  Trash2,
  Menu,
  X,
  Type
} from 'lucide-react';

export interface ThemeSettings {
  presetName: string;
  colorPrimary: string;
  colorSecondary: string;
  colorBackground: string;
  colorCard: string;
  colorButton: string;
  colorText: string;
  fontFamily: string;
  fontSizeScale: 'compact' | 'normal' | 'large';
  appName: string;
  appTagline: string;
  logoUrl: string | null;
  logoSize: 'small' | 'medium' | 'large';
  cardRadius: string;
  buttonShape: 'pill' | 'rounded' | 'square';
}

export const DEFAULT_THEME: ThemeSettings = {
  presetName: 'Pastel Lilac',
  colorPrimary: '#9B82D8',
  colorSecondary: '#DCCEF9',
  colorBackground: '#F5F0FF',
  colorCard: '#FFFFFF',
  colorButton: '#9B82D8',
  colorText: '#454052',
  fontFamily: "'Times New Roman', Times, Georgia, serif",
  fontSizeScale: 'normal',
  appName: 'KosCare',
  appTagline: 'Perawatan Kos Jadi Lebih Mudah',
  logoUrl: null,
  logoSize: 'medium',
  cardRadius: '1rem',
  buttonShape: 'pill',
};

export const THEME_PRESETS: Record<string, { primary: string; secondary: string; background: string; card: string; button: string; text: string }> = {
  'Pastel Lilac': {
    primary: '#9B82D8',
    secondary: '#DCCEF9',
    background: '#F5F0FF',
    card: '#FFFFFF',
    button: '#9B82D8',
    text: '#454052',
  },
  'Pastel Pink': {
    primary: '#E57399',
    secondary: '#F8DCE8',
    background: '#FFF0F5',
    card: '#FFFFFF',
    button: '#E57399',
    text: '#453840',
  },
  'Baby Blue': {
    primary: '#5B92E5',
    secondary: '#DCEBFF',
    background: '#F0F5FC',
    card: '#FFFFFF',
    button: '#5B92E5',
    text: '#323E4D',
  },
  'Sage Green': {
    primary: '#7E9F7A',
    secondary: '#DCE8DB',
    background: '#F3F7F2',
    card: '#FFFFFF',
    button: '#7E9F7A',
    text: '#384236',
  },
  'Cream': {
    primary: '#B8936D',
    secondary: '#F2E4D5',
    background: '#FFFDF9',
    card: '#FFFFFF',
    button: '#B8936D',
    text: '#4A4139',
  },
};

export const AVAILABLE_FONTS = [
  { id: "'Times New Roman', Times, Georgia, serif", name: "Times New Roman", desc: "Klasik, Elegan & Bawaan Resmi" },
  { id: "Georgia, Times, serif", name: "Georgia", desc: "Elegan, Hangat & Mudah Dibaca" },
  { id: "Garamond, Baskerville, serif", name: "Garamond", desc: "Klasik Lembut & Berkarakter" },
  { id: "Arial, Helvetica, sans-serif", name: "Arial", desc: "Modern, Tegas & Bersih" },
  { id: "Verdana, Geneva, sans-serif", name: "Verdana", desc: "Jelas, Terbuka & Ramah Layar" },
  { id: "'Trebuchet MS', Lucida Sans, sans-serif", name: "Trebuchet MS", desc: "Dinamis, Ringan & Santai" },
];

interface ReportItem {
  id: number;
  ticket_number: string;
  user_id: number;
  reporter_name: string;
  reporter_room: string;
  reporter_phone: string;
  reporter_email: string;
  title: string;
  category: string;
  location: string;
  description: string;
  urgency: 'Rendah' | 'Sedang' | 'Tinggi' | 'Darurat';
  photo?: string;
  status: 'Menunggu Pemeriksaan' | 'Sedang Diproses' | 'Menunggu Informasi' | 'Selesai' | 'Ditolak';
  created_at: string;
  history: {
    changer_name: string;
    changer_role: string;
    previous_status: string;
    new_status: string;
    notes?: string;
    created_at: string;
  }[];
  comments: {
    author_name: string;
    author_role: string;
    comment: string;
    created_at: string;
  }[];
  repair_record?: {
    technician_name: string;
    completion_date: string;
    repair_notes: string;
    cost_estimate: number;
  };
}

interface UserProfile {
  id: number;
  name: string;
  username: string;
  email: string;
  phone: string;
  role: 'penghuni' | 'pengelola';
  room_number?: string;
}

const INITIAL_USERS: UserProfile[] = [
  {
    id: 1,
    name: 'Vania Raissa',
    username: 'pengelola',
    email: 'pengelola@koscare.id',
    phone: '081234567890',
    role: 'pengelola',
  },
  {
    id: 2,
    name: 'Dimas Anggoro',
    username: 'dimas',
    email: 'dimas@example.com',
    phone: '081298765432',
    role: 'penghuni',
    room_number: 'Kamar 102',
  },
  {
    id: 3,
    name: 'Siti Nurhaliza',
    username: 'siti',
    email: 'siti@example.com',
    phone: '081345678912',
    role: 'penghuni',
    room_number: 'Kamar 205',
  },
];

const INITIAL_REPORTS: ReportItem[] = [
  {
    id: 1,
    ticket_number: 'KC-202610-0001',
    user_id: 2,
    reporter_name: 'Dimas Anggoro',
    reporter_room: 'Kamar 102',
    reporter_phone: '081298765432',
    reporter_email: 'dimas@example.com',
    title: 'Kran Air Wastafel Bocor Menetes',
    category: 'Air dan sanitasi',
    location: 'Kamar 102',
    description: 'Kran wastafel tidak bisa diputar rapat, air mengalir terus menerus sejak kemarin malam sehingga lantai basah.',
    urgency: 'Tinggi',
    photo: 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=600&q=80',
    status: 'Sedang Diproses',
    created_at: '2026-10-18',
    history: [
      {
        changer_name: 'Dimas Anggoro',
        changer_role: 'penghuni',
        previous_status: 'Baru Dibuat',
        new_status: 'Menunggu Pemeriksaan',
        notes: 'Laporan kerusakan fasilitas diajukan oleh penghuni.',
        created_at: '18 Okt 2026, 09:30 WIB',
      },
      {
        changer_name: 'Vania Raissa',
        changer_role: 'pengelola',
        previous_status: 'Menunggu Pemeriksaan',
        new_status: 'Sedang Diproses',
        notes: 'Laporan telah diverifikasi. Teknisi dijadwalkan datang untuk penggantian seal dan kran.',
        created_at: '18 Okt 2026, 10:00 WIB',
      },
    ],
    comments: [
      {
        author_name: 'Vania Raissa',
        author_role: 'pengelola',
        comment: 'Halo Mas Dimas, tim perbaikan dijadwalkan ke kamar sekitar jam 15.00 WIB untuk cek kran.',
        created_at: '18 Okt 2026, 10:05 WIB',
      },
      {
        author_name: 'Dimas Anggoro',
        author_role: 'penghuni',
        comment: 'Baik Bu Vania, saya ada di kamar setelah jam 14.30. Terima kasih.',
        created_at: '18 Okt 2026, 10:15 WIB',
      },
    ],
  },
  {
    id: 2,
    ticket_number: 'KC-202610-0002',
    user_id: 2,
    reporter_name: 'Dimas Anggoro',
    reporter_room: 'Kamar 102',
    reporter_phone: '081298765432',
    reporter_email: 'dimas@example.com',
    title: 'Lampu Kamar Mandi Mati',
    category: 'Listrik',
    location: 'Kamar 102',
    description: 'Lampu downlight kamar mandi mendadak padam saat dinyalakan tadi pagi.',
    urgency: 'Sedang',
    status: 'Menunggu Pemeriksaan',
    created_at: '2026-10-19',
    history: [
      {
        changer_name: 'Dimas Anggoro',
        changer_role: 'penghuni',
        previous_status: 'Baru Dibuat',
        new_status: 'Menunggu Pemeriksaan',
        notes: 'Laporan diajukan penghuni.',
        created_at: '19 Okt 2026, 14:15 WIB',
      },
    ],
    comments: [],
  },
  {
    id: 3,
    ticket_number: 'KC-202610-0003',
    user_id: 3,
    reporter_name: 'Siti Nurhaliza',
    reporter_room: 'Kamar 205',
    reporter_phone: '081345678912',
    reporter_email: 'siti@example.com',
    title: 'Kunci Silinder Pintu Kamar Macet',
    category: 'Bangunan',
    location: 'Kamar 205',
    description: 'Kunci silinder sulit diputar dari dalam, khawatir sewaktu-waktu terkunci.',
    urgency: 'Darurat',
    status: 'Selesai',
    created_at: '2026-10-17',
    history: [
      {
        changer_name: 'Siti Nurhaliza',
        changer_role: 'penghuni',
        previous_status: 'Baru Dibuat',
        new_status: 'Menunggu Pemeriksaan',
        notes: 'Laporan diajukan.',
        created_at: '17 Okt 2026, 11:00 WIB',
      },
      {
        changer_name: 'Vania Raissa',
        changer_role: 'pengelola',
        previous_status: 'Menunggu Pemeriksaan',
        new_status: 'Sedang Diproses',
        notes: 'Segera ditangani.',
        created_at: '17 Okt 2026, 11:30 WIB',
      },
      {
        changer_name: 'Vania Raissa',
        changer_role: 'pengelola',
        previous_status: 'Sedang Diproses',
        new_status: 'Selesai',
        notes: 'Silinder kunci pintu kamar 205 telah diganti dengan unit baru Dekkson.',
        created_at: '17 Okt 2026, 14:00 WIB',
      },
    ],
    comments: [],
    repair_record: {
      technician_name: 'Pak Joko Santoso',
      completion_date: '17 Oktober 2026',
      repair_notes: 'Penggantian 1 set lockcase dan silinder pintu Kamar 205. Diserahkan 3 anak kunci baru.',
      cost_estimate: 125000,
    },
  },
];

export default function App() {
  const [currentPage, setCurrentPage] = useState<string>('home'); // 'home' | 'login' | 'register' | 'dashboard' | 'create_report' | 'report_detail' | 'repair_history' | 'profile' | 'theme_settings'
  const [loginRoleTarget, setLoginRoleTarget] = useState<'penghuni' | 'pengelola'>('penghuni');
  const [currentUser, setCurrentUser] = useState<UserProfile | null>(null);
  const [mobileMenuOpen, setMobileMenuOpen] = useState<boolean>(false);

  // Theme Settings state with localStorage persistence
  const [theme, setTheme] = useState<ThemeSettings>(() => {
    try {
      const saved = localStorage.getItem('koscare_theme_settings');
      if (saved) return { ...DEFAULT_THEME, ...JSON.parse(saved) };
    } catch (e) {}
    return DEFAULT_THEME;
  });

  const [draftTheme, setDraftTheme] = useState<ThemeSettings>(theme);
  const [customFontSample, setCustomFontSample] = useState<string>('');

  const [users, setUsers] = useState<UserProfile[]>(INITIAL_USERS);
  const [reports, setReports] = useState<ReportItem[]>(INITIAL_REPORTS);
  const [selectedReportId, setSelectedReportId] = useState<number | null>(null);

  const [filterStatus, setFilterStatus] = useState<string>('');
  const [filterCategory, setFilterCategory] = useState<string>('');
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [notification, setNotification] = useState<string | null>(null);

  const showNotification = (msg: string) => {
    setNotification(msg);
    setTimeout(() => setNotification(null), 4000);
  };

  const handleLogout = () => {
    setCurrentUser(null);
    setCurrentPage('home');
    setMobileMenuOpen(false);
    showNotification('Anda telah berhasil keluar.');
  };

  const handleSaveTheme = () => {
    setTheme(draftTheme);
    try {
      localStorage.setItem('koscare_theme_settings', JSON.stringify(draftTheme));
    } catch (e) {}
    showNotification('Pengaturan tema & tipografi berhasil disimpan secara permanen.');
  };

  const handleResetTheme = () => {
    setTheme(DEFAULT_THEME);
    setDraftTheme(DEFAULT_THEME);
    try {
      localStorage.removeItem('koscare_theme_settings');
    } catch (e) {}
    showNotification('Tema dan identitas dikembalikan ke setelan bawaan (Pastel Lilac & Times New Roman).');
  };

  const handleCancelTheme = () => {
    setDraftTheme({ ...theme });
    showNotification('Perubahan tampilan dibatalkan.');
  };

  const renderStatusBadge = (status: string) => {
    const map: Record<string, { bg: string; text: string }> = {
      'Menunggu Pemeriksaan': { bg: '#FFF3CD', text: '#856404' },
      'Sedang Diproses': { bg: theme.colorSecondary, text: theme.colorText },
      'Menunggu Informasi': { bg: '#DCEBFF', text: '#2B517D' },
      'Selesai': { bg: '#E2F0D9', text: '#2B542C' },
      'Ditolak': { bg: '#FCE8E6', text: '#8A332A' },
    };
    const c = map[status] || { bg: theme.colorSecondary, text: theme.colorText };
    return (
      <span
        className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap"
        style={{ backgroundColor: c.bg, color: c.text }}
      >
        {status}
      </span>
    );
  };

  const renderUrgencyBadge = (urgency: string) => {
    const map: Record<string, { bg: string; text: string }> = {
      Rendah: { bg: theme.colorSecondary, text: theme.colorText },
      Sedang: { bg: '#DCEBFF', text: '#2B517D' },
      Tinggi: { bg: '#F8DCE8', text: '#7A2E49' },
      Darurat: { bg: '#FCE8E6', text: '#8A332A' },
    };
    const c = map[urgency] || { bg: theme.colorSecondary, text: theme.colorText };
    return (
      <span
        className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap"
        style={{ backgroundColor: c.bg, color: c.text }}
      >
        {urgency}
      </span>
    );
  };

  const currentBtnRadius = theme.buttonShape === 'pill' ? '9999px' : theme.buttonShape === 'rounded' ? '10px' : '4px';

  return (
    <div
      className="min-h-screen flex flex-col transition-colors duration-200"
      style={{
        backgroundColor: theme.colorBackground,
        color: theme.colorText,
        fontFamily: theme.fontFamily,
      }}
    >
      {/* Injeksi Style CSS Dinamis untuk Seluruh Komponen Website */}
      <style>{`
        :root {
          --app-primary: ${theme.colorPrimary};
          --app-secondary: ${theme.colorSecondary};
          --app-bg: ${theme.colorBackground};
          --app-card: ${theme.colorCard};
          --app-btn: ${theme.colorButton};
          --app-text: ${theme.colorText};
          --app-card-radius: ${theme.cardRadius};
          --app-btn-radius: ${currentBtnRadius};
        }
        *, *::before, *::after, body, button, input, textarea, select, table, th, td {
          font-family: ${theme.fontFamily} !important;
        }
        html {
          font-size: ${theme.fontSizeScale === 'large' ? '17px' : theme.fontSizeScale === 'compact' ? '15px' : '16px'};
        }
      `}</style>

      {/* Header & Navbar Aesthetic (Responsif Tablet & Ponsel) */}
      <header
        className="border-b sticky top-0 z-30 backdrop-blur-md transition-colors"
        style={{
          backgroundColor: `${theme.colorCard}F5`,
          borderColor: theme.colorSecondary,
        }}
      >
        <div className="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
          {/* Logo & Nama Aplikasi */}
          <button
            onClick={() => {
              setCurrentPage(currentUser ? 'dashboard' : 'home');
              setMobileMenuOpen(false);
            }}
            className="flex items-center gap-2.5 text-lg sm:text-xl font-bold hover:opacity-85 transition flex-shrink-0"
            style={{ color: theme.colorText }}
          >
            {theme.logoUrl ? (
              <img
                src={theme.logoUrl}
                alt={theme.appName}
                style={{
                  height: theme.logoSize === 'small' ? '26px' : theme.logoSize === 'large' ? '42px' : '34px',
                  maxWidth: '120px',
                  objectFit: 'contain',
                }}
              />
            ) : (
              <span
                className="w-8 h-8 rounded-full flex items-center justify-center transition flex-shrink-0"
                style={{ backgroundColor: theme.colorSecondary, color: theme.colorButton }}
              >
                <House className="w-4 h-4" />
              </span>
            )}
            <span className="truncate">{theme.appName}</span>
          </button>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-3 text-sm">
            {currentUser ? (
              <>
                {currentUser.role === 'penghuni' ? (
                  <>
                    <button
                      onClick={() => setCurrentPage('dashboard')}
                      className={`hover:opacity-80 px-2.5 py-1 rounded-lg transition ${currentPage === 'dashboard' ? 'font-bold' : ''}`}
                      style={{
                        color: currentPage === 'dashboard' ? theme.colorButton : theme.colorText,
                        backgroundColor: currentPage === 'dashboard' ? `${theme.colorSecondary}50` : 'transparent',
                      }}
                    >
                      Dashboard
                    </button>
                    <button
                      onClick={() => setCurrentPage('create_report')}
                      className={`hover:opacity-80 px-2.5 py-1 rounded-lg transition ${currentPage === 'create_report' ? 'font-bold' : ''}`}
                      style={{
                        color: currentPage === 'create_report' ? theme.colorButton : theme.colorText,
                        backgroundColor: currentPage === 'create_report' ? `${theme.colorSecondary}50` : 'transparent',
                      }}
                    >
                      Buat Laporan
                    </button>
                  </>
                ) : (
                  <>
                    <button
                      onClick={() => setCurrentPage('dashboard')}
                      className={`hover:opacity-80 px-2.5 py-1 rounded-lg transition ${currentPage === 'dashboard' ? 'font-bold' : ''}`}
                      style={{
                        color: currentPage === 'dashboard' ? theme.colorButton : theme.colorText,
                        backgroundColor: currentPage === 'dashboard' ? `${theme.colorSecondary}50` : 'transparent',
                      }}
                    >
                      Dashboard
                    </button>
                    <button
                      onClick={() => setCurrentPage('repair_history')}
                      className={`hover:opacity-80 px-2.5 py-1 rounded-lg transition ${currentPage === 'repair_history' ? 'font-bold' : ''}`}
                      style={{
                        color: currentPage === 'repair_history' ? theme.colorButton : theme.colorText,
                        backgroundColor: currentPage === 'repair_history' ? `${theme.colorSecondary}50` : 'transparent',
                      }}
                    >
                      Riwayat Perbaikan
                    </button>
                    <button
                      onClick={() => {
                        setDraftTheme({ ...theme });
                        setCurrentPage('theme_settings');
                      }}
                      className={`hover:opacity-80 px-2.5 py-1 rounded-lg transition flex items-center gap-1.5 ${currentPage === 'theme_settings' ? 'font-bold' : ''}`}
                      style={{
                        color: currentPage === 'theme_settings' ? theme.colorButton : theme.colorText,
                        backgroundColor: currentPage === 'theme_settings' ? `${theme.colorSecondary}50` : 'transparent',
                      }}
                    >
                      <Palette className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                      <span>Pengaturan Tampilan</span>
                    </button>
                  </>
                )}

                <div className="flex items-center gap-2 pl-2 border-l" style={{ borderColor: theme.colorSecondary }}>
                  <button
                    onClick={() => setCurrentPage('profile')}
                    className="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition"
                    style={{
                      backgroundColor: theme.colorSecondary,
                      color: theme.colorText,
                      borderRadius: currentBtnRadius,
                    }}
                  >
                    <User className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                    <span className="truncate max-w-[120px]">{currentUser.name}</span>
                  </button>
                  <button
                    onClick={handleLogout}
                    className="p-1.5 rounded transition hover:opacity-75"
                    style={{ color: theme.colorText }}
                    title="Keluar"
                  >
                    <LogOut className="w-4 h-4" />
                  </button>
                </div>
              </>
            ) : (
              <div className="flex items-center gap-2">
                <button
                  onClick={() => {
                    setLoginRoleTarget('penghuni');
                    setCurrentPage('login');
                  }}
                  className="px-4 py-1.5 border transition text-xs font-semibold"
                  style={{
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                    backgroundColor: theme.colorCard,
                  }}
                >
                  Masuk
                </button>
                <button
                  onClick={() => setCurrentPage('register')}
                  className="px-4 py-1.5 text-white transition text-xs font-bold shadow-xs hover:opacity-90"
                  style={{
                    backgroundColor: theme.colorButton,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Registrasi
                </button>
              </div>
            )}
          </div>

          {/* Mobile Menu Button (< md) */}
          <div className="flex md:hidden items-center gap-2">
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="p-2 rounded-xl border transition"
              style={{
                borderColor: theme.colorSecondary,
                backgroundColor: theme.colorCard,
                color: theme.colorText,
              }}
              aria-label="Menu"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>
          </div>
        </div>

        {/* Mobile Dropdown Menu */}
        {mobileMenuOpen && (
          <div
            className="md:hidden border-t px-4 py-3 space-y-2"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
            }}
          >
            {currentUser ? (
              <>
                <div className="pb-2 mb-2 border-b flex items-center justify-between" style={{ borderColor: theme.colorSecondary }}>
                  <div className="flex items-center gap-2">
                    <User className="w-4 h-4" style={{ color: theme.colorButton }} />
                    <span className="text-xs font-bold" style={{ color: theme.colorText }}>
                      {currentUser.name} ({currentUser.role === 'pengelola' ? 'Pengelola' : currentUser.room_number || 'Penghuni'})
                    </span>
                  </div>
                  <button
                    onClick={handleLogout}
                    className="text-xs text-red-600 font-bold flex items-center gap-1"
                  >
                    <LogOut className="w-3.5 h-3.5" /> Keluar
                  </button>
                </div>

                <button
                  onClick={() => {
                    setCurrentPage('dashboard');
                    setMobileMenuOpen(false);
                  }}
                  className="w-full text-left py-2 px-3 rounded-lg text-xs font-semibold"
                  style={{
                    backgroundColor: currentPage === 'dashboard' ? theme.colorSecondary : 'transparent',
                    color: theme.colorText,
                  }}
                >
                  Dashboard
                </button>

                {currentUser.role === 'penghuni' && (
                  <button
                    onClick={() => {
                      setCurrentPage('create_report');
                      setMobileMenuOpen(false);
                    }}
                    className="w-full text-left py-2 px-3 rounded-lg text-xs font-semibold"
                    style={{
                      backgroundColor: currentPage === 'create_report' ? theme.colorSecondary : 'transparent',
                      color: theme.colorText,
                    }}
                  >
                    Buat Laporan Kerusakan
                  </button>
                )}

                {currentUser.role === 'pengelola' && (
                  <>
                    <button
                      onClick={() => {
                        setCurrentPage('repair_history');
                        setMobileMenuOpen(false);
                      }}
                      className="w-full text-left py-2 px-3 rounded-lg text-xs font-semibold"
                      style={{
                        backgroundColor: currentPage === 'repair_history' ? theme.colorSecondary : 'transparent',
                        color: theme.colorText,
                      }}
                    >
                      Riwayat Perbaikan Selesai
                    </button>
                    <button
                      onClick={() => {
                        setDraftTheme({ ...theme });
                        setCurrentPage('theme_settings');
                        setMobileMenuOpen(false);
                      }}
                      className="w-full text-left py-2 px-3 rounded-lg text-xs font-semibold flex items-center gap-2"
                      style={{
                        backgroundColor: currentPage === 'theme_settings' ? theme.colorSecondary : 'transparent',
                        color: theme.colorText,
                      }}
                    >
                      <Palette className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                      <span>Pengaturan Tampilan</span>
                    </button>
                  </>
                )}

                <button
                  onClick={() => {
                    setCurrentPage('profile');
                    setMobileMenuOpen(false);
                  }}
                  className="w-full text-left py-2 px-3 rounded-lg text-xs font-semibold"
                  style={{
                    backgroundColor: currentPage === 'profile' ? theme.colorSecondary : 'transparent',
                    color: theme.colorText,
                  }}
                >
                  Profil Akun
                </button>
              </>
            ) : (
              <div className="flex flex-col gap-2 pt-1">
                <button
                  onClick={() => {
                    setLoginRoleTarget('penghuni');
                    setCurrentPage('login');
                    setMobileMenuOpen(false);
                  }}
                  className="w-full py-2.5 border text-center text-xs font-bold"
                  style={{
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Masuk Akun
                </button>
                <button
                  onClick={() => {
                    setCurrentPage('register');
                    setMobileMenuOpen(false);
                  }}
                  className="w-full py-2.5 text-center text-white text-xs font-bold shadow-xs"
                  style={{
                    backgroundColor: theme.colorButton,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Registrasi Penghuni
                </button>
              </div>
            )}
          </div>
        )}
      </header>

      {/* Notifikasi Halus */}
      {notification && (
        <div className="max-w-md mx-auto mt-4 px-4 w-full">
          <div
            className="p-3 shadow-sm flex items-center justify-between text-xs border"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              color: theme.colorText,
              borderRadius: theme.cardRadius,
            }}
          >
            <div className="flex items-center gap-2">
              <Check className="w-4 h-4" style={{ color: theme.colorButton }} />
              <span>{notification}</span>
            </div>
            <button onClick={() => setNotification(null)} className="opacity-60 hover:opacity-100">
              ✕
            </button>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* 1. HALAMAN UTAMA (LANDING PAGE SESUAI KETENTUAN SECTION 3) */}
      {/* ========================================================================= */}
      {currentPage === 'home' && (
        <main className="flex-1 flex flex-col justify-center items-center py-12 sm:py-16 px-4">
          <div className="max-w-lg w-full text-center">
            {/* Logo / Icon KosCare */}
            <div className="mb-4 inline-flex items-center justify-center">
              {theme.logoUrl ? (
                <img
                  src={theme.logoUrl}
                  alt={theme.appName}
                  style={{
                    height: '72px',
                    maxWidth: '180px',
                    objectFit: 'contain',
                  }}
                />
              ) : (
                <div
                  className="w-16 h-16 rounded-full flex items-center justify-center shadow-sm"
                  style={{
                    background: `linear-gradient(135deg, ${theme.colorSecondary}, ${theme.colorButton})`,
                    color: '#FFFFFF',
                  }}
                >
                  <House className="w-8 h-8" />
                </div>
              )}
            </div>

            {/* Nama & Tagline Resmi */}
            <h1 className="text-3xl sm:text-5xl font-bold tracking-tight mb-2" style={{ color: theme.colorText }}>
              {theme.appName}
            </h1>
            <p className="text-base sm:text-xl italic mb-6 sm:mb-8" style={{ color: theme.colorText }}>
              “{theme.appTagline}”
            </p>

            {/* Tiga Pilihan Utama yang Paling Menonjol */}
            <div
              className="p-6 sm:p-8 border shadow-sm text-left mb-6"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                borderRadius: theme.cardRadius,
              }}
            >
              <h2 className="text-center font-bold text-base sm:text-lg mb-5" style={{ color: theme.colorText }}>
                Pilih Akses Masuk
              </h2>

              <div className="space-y-3">
                {/* 1. Tombol Masuk sebagai Pengelola */}
                <button
                  onClick={() => {
                    setLoginRoleTarget('pengelola');
                    setCurrentPage('login');
                  }}
                  className="w-full py-3.5 px-5 border font-bold text-sm flex items-center justify-center gap-2 transition hover:opacity-90 shadow-2xs"
                  style={{
                    borderColor: theme.colorSecondary,
                    backgroundColor: theme.colorCard,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  <ShieldCheck className="w-4 h-4" style={{ color: theme.colorButton }} />
                  <span>Masuk sebagai Pengelola</span>
                </button>

                {/* 2. Tombol Masuk sebagai Penghuni */}
                <button
                  onClick={() => {
                    setLoginRoleTarget('penghuni');
                    setCurrentPage('login');
                  }}
                  className="w-full py-3.5 px-5 text-white font-bold text-sm flex items-center justify-center gap-2 transition shadow-sm hover:opacity-95"
                  style={{
                    backgroundColor: theme.colorButton,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  <User className="w-4 h-4" />
                  <span>Masuk sebagai Penghuni</span>
                </button>

                {/* 3. Tombol atau Tautan Registrasi Penghuni */}
                <button
                  onClick={() => setCurrentPage('register')}
                  className="w-full py-3.5 px-5 font-bold text-sm flex items-center justify-center gap-2 transition hover:opacity-90"
                  style={{
                    backgroundColor: theme.colorSecondary,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  <PlusCircle className="w-4 h-4" style={{ color: theme.colorButton }} />
                  <span>Registrasi Penghuni</span>
                </button>
              </div>
            </div>

            <p className="text-xs opacity-75" style={{ color: theme.colorText }}>
              Sistem pelaporan dan pemantauan kerusakan fasilitas kos yang rapi, tenang, dan teratur.
            </p>
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 2. HALAMAN LOGIN */}
      {/* ========================================================================= */}
      {currentPage === 'login' && (
        <main className="flex-1 flex items-center justify-center py-10 sm:py-14 px-4">
          <div
            className="max-w-md w-full p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div className="text-center mb-6">
              <span
                className="w-12 h-12 rounded-full inline-flex items-center justify-center mb-3"
                style={{
                  backgroundColor: theme.colorSecondary,
                  color: theme.colorButton,
                }}
              >
                {loginRoleTarget === 'pengelola' ? <ShieldCheck className="w-6 h-6" /> : <User className="w-6 h-6" />}
              </span>
              <h2 className="text-2xl font-bold" style={{ color: theme.colorText }}>
                Masuk sebagai {loginRoleTarget === 'pengelola' ? 'Pengelola' : 'Penghuni'}
              </h2>
              <p className="text-xs opacity-75 mt-1" style={{ color: theme.colorText }}>
                Silakan masukkan akun Anda untuk melanjutkan.
              </p>
            </div>

            <form
              onSubmit={e => {
                e.preventDefault();
                const form = e.currentTarget;
                const identity = (form.elements.namedItem('identity') as HTMLInputElement).value.trim();
                const user = users.find(
                  u => (u.username === identity || u.email === identity) && u.role === loginRoleTarget
                ) || users.find(u => u.role === loginRoleTarget);

                if (user) {
                  setCurrentUser(user);
                  setCurrentPage('dashboard');
                  showNotification(`Selamat datang, ${user.name}`);
                } else {
                  showNotification('Username atau kata sandi tidak sesuai.');
                }
              }}
              className="space-y-4 text-xs"
            >
              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Username atau Email *
                </label>
                <input
                  name="identity"
                  required
                  defaultValue={loginRoleTarget === 'pengelola' ? 'pengelola' : 'dimas'}
                  placeholder="Masukkan username atau email"
                  className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
              </div>

              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Kata Sandi *
                </label>
                <input
                  type="password"
                  name="password"
                  required
                  defaultValue="Password123"
                  placeholder="Masukkan kata sandi"
                  className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
              </div>

              <button
                type="submit"
                className="w-full py-3 text-white font-bold text-sm shadow-xs transition hover:opacity-95"
                style={{
                  backgroundColor: theme.colorButton,
                  borderRadius: currentBtnRadius,
                }}
              >
                Masuk
              </button>

              <div
                className="text-center pt-3 border-t space-y-2"
                style={{ borderColor: `${theme.colorSecondary}80` }}
              >
                {loginRoleTarget === 'penghuni' && (
                  <p className="text-xs opacity-80" style={{ color: theme.colorText }}>
                    Belum memiliki akun?{' '}
                    <button
                      type="button"
                      onClick={() => setCurrentPage('register')}
                      className="font-bold hover:underline"
                      style={{ color: theme.colorButton }}
                    >
                      Registrasi di sini
                    </button>
                  </p>
                )}
                <div>
                  <button
                    type="button"
                    onClick={() => {
                      setLoginRoleTarget(loginRoleTarget === 'pengelola' ? 'penghuni' : 'pengelola');
                    }}
                    className="text-xs hover:underline opacity-80"
                    style={{ color: theme.colorText }}
                  >
                    Beralih ke Masuk sebagai {loginRoleTarget === 'pengelola' ? 'Penghuni' : 'Pengelola'}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 3. HALAMAN REGISTRASI PENGHUNI */}
      {/* ========================================================================= */}
      {currentPage === 'register' && (
        <main className="flex-1 flex items-center justify-center py-10 sm:py-14 px-4">
          <div
            className="max-w-lg w-full p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div className="text-center mb-6">
              <span
                className="w-12 h-12 rounded-full inline-flex items-center justify-center mb-3"
                style={{
                  backgroundColor: theme.colorSecondary,
                  color: theme.colorButton,
                }}
              >
                <PlusCircle className="w-6 h-6" />
              </span>
              <h2 className="text-2xl font-bold" style={{ color: theme.colorText }}>
                Registrasi Penghuni
              </h2>
              <p className="text-xs opacity-75 mt-1" style={{ color: theme.colorText }}>
                Daftarkan kamar Anda untuk mulai mengajukan keluhan fasilitas.
              </p>
            </div>

            <form
              onSubmit={e => {
                e.preventDefault();
                const form = e.currentTarget;
                const name = (form.elements.namedItem('reg_name') as HTMLInputElement).value.trim();
                const username = (form.elements.namedItem('reg_username') as HTMLInputElement).value.trim();
                const room = (form.elements.namedItem('reg_room') as HTMLInputElement).value.trim();
                const email = (form.elements.namedItem('reg_email') as HTMLInputElement).value.trim();
                const phone = (form.elements.namedItem('reg_phone') as HTMLInputElement).value.trim();

                const newUser: UserProfile = {
                  id: users.length + 1,
                  name,
                  username,
                  email,
                  phone,
                  room_number: room,
                  role: 'penghuni',
                };

                setUsers([...users, newUser]);
                setCurrentUser(newUser);
                setCurrentPage('dashboard');
                showNotification(`Pendaftaran berhasil. Selamat datang, ${name}!`);
              }}
              className="space-y-3.5 text-xs"
            >
              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Nama Lengkap *
                </label>
                <input
                  name="reg_name"
                  required
                  placeholder="Contoh: Budi Santoso"
                  className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Username *
                  </label>
                  <input
                    name="reg_username"
                    required
                    placeholder="budi.santoso"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Nomor Kamar *
                  </label>
                  <input
                    name="reg_room"
                    required
                    placeholder="Kamar 105"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Email *
                  </label>
                  <input
                    type="email"
                    name="reg_email"
                    required
                    placeholder="nama@email.com"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    No. WhatsApp / HP *
                  </label>
                  <input
                    name="reg_phone"
                    required
                    placeholder="081234567890"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Kata Sandi *
                  </label>
                  <input
                    type="password"
                    required
                    minLength={8}
                    defaultValue="Penghuni123"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Ulangi Sandi *
                  </label>
                  <input
                    type="password"
                    required
                    minLength={8}
                    defaultValue="Penghuni123"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
              </div>

              <button
                type="submit"
                className="w-full py-3 text-white font-bold text-sm shadow-xs transition hover:opacity-95 mt-2"
                style={{
                  backgroundColor: theme.colorButton,
                  borderRadius: currentBtnRadius,
                }}
              >
                Daftar Akun
              </button>

              <div className="text-center pt-2">
                <button
                  type="button"
                  onClick={() => {
                    setLoginRoleTarget('penghuni');
                    setCurrentPage('login');
                  }}
                  className="text-xs hover:underline opacity-80"
                  style={{ color: theme.colorText }}
                >
                  Sudah memiliki akun? Masuk di sini
                </button>
              </div>
            </form>
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 4. DASHBOARD PENGHUNI */}
      {/* ========================================================================= */}
      {currentPage === 'dashboard' && currentUser?.role === 'penghuni' && (
        <main className="flex-1 max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 w-full">
          {/* Sapaan Singkat */}
          <div
            className="p-6 sm:p-8 border shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div>
              <span
                className="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2"
                style={{
                  backgroundColor: theme.colorSecondary,
                  color: theme.colorText,
                }}
              >
                Penghuni • {currentUser.room_number || 'Kamar 102'}
              </span>
              <h2 className="text-2xl font-bold" style={{ color: theme.colorText }}>
                Halo, {currentUser.name}
              </h2>
              <p className="text-xs opacity-75 mt-0.5" style={{ color: theme.colorText }}>
                Pantau fasilitas kamar Anda dan laporkan jika terdapat kerusakan.
              </p>
            </div>
            <div>
              {/* Tombol Utama Buat Laporan */}
              <button
                onClick={() => setCurrentPage('create_report')}
                className="px-5 py-2.5 text-white text-xs font-bold shadow-xs flex items-center gap-2 transition hover:opacity-95"
                style={{
                  backgroundColor: theme.colorButton,
                  borderRadius: currentBtnRadius,
                }}
              >
                <PlusCircle className="w-4 h-4" />
                <span>Buat Laporan</span>
              </button>
            </div>
          </div>

          {/* Ringkasan 3 Pilar */}
          {(() => {
            const myReports = reports.filter(r => r.user_id === currentUser.id);
            const totalCount = myReports.length;
            const inProgressCount = myReports.filter(
              r => r.status === 'Menunggu Pemeriksaan' || r.status === 'Sedang Diproses' || r.status === 'Menunggu Informasi'
            ).length;
            const completedCount = myReports.filter(r => r.status === 'Selesai').length;

            return (
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block opacity-75" style={{ color: theme.colorText }}>
                    Total Laporan
                  </span>
                  <div className="text-2xl font-bold my-1" style={{ color: theme.colorText }}>
                    {totalCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Keluhan yang diajukan
                  </span>
                </div>

                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block font-semibold" style={{ color: theme.colorButton }}>
                    Sedang Diproses
                  </span>
                  <div className="text-2xl font-bold my-1" style={{ color: theme.colorButton }}>
                    {inProgressCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Dalam antrean / perbaikan
                  </span>
                </div>

                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block font-semibold text-emerald-700">
                    Selesai
                  </span>
                  <div className="text-2xl font-bold my-1 text-emerald-700">
                    {completedCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Telah diperbaiki
                  </span>
                </div>
              </div>
            );
          })()}

          {/* Daftar Laporan Kerusakan Milik Penghuni */}
          <div
            className="p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div
              className="flex justify-between items-center pb-4 border-b mb-4"
              style={{ borderColor: `${theme.colorSecondary}80` }}
            >
              <h3 className="font-bold text-base" style={{ color: theme.colorText }}>
                Laporan Kerusakan Anda
              </h3>
              <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                {reports.filter(r => r.user_id === currentUser.id).length} Laporan
              </span>
            </div>

            {(() => {
              const myReports = reports.filter(r => r.user_id === currentUser.id);

              if (myReports.length === 0) {
                return (
                  <div className="text-center py-12 text-xs opacity-75" style={{ color: theme.colorText }}>
                    <p className="mb-3">Belum ada laporan kerusakan yang diajukan.</p>
                    <button
                      onClick={() => setCurrentPage('create_report')}
                      className="px-4 py-2 text-white font-semibold text-xs shadow-xs"
                      style={{
                        backgroundColor: theme.colorButton,
                        borderRadius: currentBtnRadius,
                      }}
                    >
                      Buat Laporan Pertama
                    </button>
                  </div>
                );
              }

              return (
                <div className="w-full overflow-x-auto -mx-2 sm:mx-0">
                  <table className="w-full text-left text-xs min-w-[640px]">
                    <thead>
                      <tr
                        className="border-b font-bold"
                        style={{
                          borderColor: theme.colorSecondary,
                          color: theme.colorText,
                        }}
                      >
                        <th className="py-2.5 px-3 whitespace-nowrap">Tanggal</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Judul & Kategori</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Lokasi</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Urgensi</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Status</th>
                        <th className="py-2.5 px-3 text-right whitespace-nowrap">Rincian</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y" style={{ borderColor: `${theme.colorSecondary}40` }}>
                      {myReports.map(r => (
                        <tr key={r.id} className="hover:bg-black/2 transition">
                          <td className="py-3 px-3 opacity-75">{r.created_at}</td>
                          <td className="py-3 px-3">
                            <div className="font-bold">{r.title}</div>
                            <div className="text-[11px] opacity-75">{r.category}</div>
                          </td>
                          <td className="py-3 px-3">{r.location}</td>
                          <td className="py-3 px-3">{renderUrgencyBadge(r.urgency)}</td>
                          <td className="py-3 px-3">{renderStatusBadge(r.status)}</td>
                          <td className="py-3 px-3 text-right">
                            <button
                              onClick={() => {
                                setSelectedReportId(r.id);
                                setCurrentPage('report_detail');
                              }}
                              className="px-3.5 py-1 border font-semibold transition hover:opacity-85"
                              style={{
                                borderColor: theme.colorSecondary,
                                color: theme.colorText,
                                borderRadius: currentBtnRadius,
                              }}
                            >
                              Lihat
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              );
            })()}
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 5. DASHBOARD PENGELOLA */}
      {/* ========================================================================= */}
      {currentPage === 'dashboard' && currentUser?.role === 'pengelola' && (
        <main className="flex-1 max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 w-full">
          {/* Header Pengelola */}
          <div
            className="p-6 sm:p-8 border shadow-sm mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div>
              <span
                className="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-2"
                style={{
                  backgroundColor: theme.colorSecondary,
                  color: theme.colorText,
                }}
              >
                Panel Pengelola
              </span>
              <h2 className="text-2xl font-bold" style={{ color: theme.colorText }}>
                Pengelolaan Laporan — {currentUser.name}
              </h2>
              <p className="text-xs opacity-75 mt-0.5" style={{ color: theme.colorText }}>
                Periksa keluhan fasilitas kos, tindak lanjuti status, dan koordinasikan perbaikan.
              </p>
            </div>
            <div className="flex flex-wrap items-center gap-2">
              <button
                onClick={() => setCurrentPage('repair_history')}
                className="px-4 py-2 border text-xs font-semibold flex items-center gap-1.5 transition hover:opacity-85"
                style={{
                  borderColor: theme.colorSecondary,
                  color: theme.colorText,
                  borderRadius: currentBtnRadius,
                  backgroundColor: theme.colorCard,
                }}
              >
                <Clock className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                <span>Riwayat Perbaikan</span>
              </button>
              <button
                onClick={() => {
                  setDraftTheme({ ...theme });
                  setCurrentPage('theme_settings');
                }}
                className="px-4 py-2 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition hover:opacity-95"
                style={{
                  backgroundColor: theme.colorButton,
                  borderRadius: currentBtnRadius,
                }}
              >
                <Palette className="w-3.5 h-3.5" />
                <span>Pengaturan Tampilan</span>
              </button>
            </div>
          </div>

          {/* Ringkasan 4 Kolom */}
          {(() => {
            const totalAll = reports.length;
            const waitingCount = reports.filter(r => r.status === 'Menunggu Pemeriksaan').length;
            const processingCount = reports.filter(r => r.status === 'Sedang Diproses').length;
            const completedCount = reports.filter(r => r.status === 'Selesai').length;

            return (
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block opacity-75" style={{ color: theme.colorText }}>
                    Semua Laporan
                  </span>
                  <div className="text-2xl font-bold my-1" style={{ color: theme.colorText }}>
                    {totalAll}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Total masuk
                  </span>
                </div>

                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block font-semibold text-amber-700">
                    Menunggu
                  </span>
                  <div className="text-2xl font-bold my-1 text-amber-700">
                    {waitingCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Perlu verifikasi
                  </span>
                </div>

                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block font-semibold" style={{ color: theme.colorButton }}>
                    Sedang Diproses
                  </span>
                  <div className="text-2xl font-bold my-1" style={{ color: theme.colorButton }}>
                    {processingCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Dalam pengerjaan
                  </span>
                </div>

                <div
                  className="p-5 border shadow-xs text-center"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: `${theme.colorSecondary}90`,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <span className="text-xs uppercase tracking-wider block font-semibold text-emerald-700">
                    Selesai
                  </span>
                  <div className="text-2xl font-bold my-1 text-emerald-700">
                    {completedCount}
                  </div>
                  <span className="text-xs opacity-75" style={{ color: theme.colorText }}>
                    Tuntas diperbaiki
                  </span>
                </div>
              </div>
            );
          })()}

          {/* Filter & Pencarian Minimalis */}
          <div
            className="p-4 border shadow-xs mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: `${theme.colorSecondary}90`,
              borderRadius: theme.cardRadius,
            }}
          >
            <div className="relative">
              <Search className="w-3.5 h-3.5 absolute left-3 top-3 opacity-50" style={{ color: theme.colorText }} />
              <input
                type="text"
                placeholder="Cari judul, kamar, pelapor..."
                value={searchQuery}
                onChange={e => setSearchQuery(e.target.value)}
                className="w-full pl-8 pr-3 py-2 rounded-xl border focus:outline-none"
                style={{
                  backgroundColor: theme.colorCard,
                  borderColor: theme.colorSecondary,
                  color: theme.colorText,
                }}
              />
            </div>

            <select
              value={filterStatus}
              onChange={e => setFilterStatus(e.target.value)}
              className="px-3 py-2 rounded-xl border focus:outline-none"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                color: theme.colorText,
              }}
            >
              <option value="">Semua Status</option>
              <option value="Menunggu Pemeriksaan">Menunggu Pemeriksaan</option>
              <option value="Sedang Diproses">Sedang Diproses</option>
              <option value="Menunggu Informasi">Menunggu Informasi</option>
              <option value="Selesai">Selesai</option>
              <option value="Ditolak">Ditolak</option>
            </select>

            <select
              value={filterCategory}
              onChange={e => setFilterCategory(e.target.value)}
              className="px-3 py-2 rounded-xl border focus:outline-none"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                color: theme.colorText,
              }}
            >
              <option value="">Semua Kategori</option>
              <option value="Listrik">Listrik</option>
              <option value="Air dan sanitasi">Air dan sanitasi</option>
              <option value="Furnitur">Furnitur</option>
              <option value="Bangunan">Bangunan</option>
              <option value="Kebersihan">Kebersihan</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>

          {/* Tabel Seluruh Laporan */}
          <div
            className="p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div
              className="flex justify-between items-center pb-4 border-b mb-4"
              style={{ borderColor: `${theme.colorSecondary}80` }}
            >
              <h3 className="font-bold text-base" style={{ color: theme.colorText }}>
                Daftar Seluruh Laporan Masuk
              </h3>
            </div>

            {(() => {
              let list = reports;
              if (filterStatus) list = list.filter(r => r.status === filterStatus);
              if (filterCategory) list = list.filter(r => r.category === filterCategory);
              if (searchQuery) {
                const q = searchQuery.toLowerCase();
                list = list.filter(
                  r =>
                    r.title.toLowerCase().includes(q) ||
                    r.location.toLowerCase().includes(q) ||
                    r.reporter_name.toLowerCase().includes(q)
                );
              }

              if (list.length === 0) {
                return (
                  <div className="text-center py-10 text-xs opacity-75" style={{ color: theme.colorText }}>
                    Tidak ditemukan laporan yang sesuai kriteria.
                  </div>
                );
              }

              return (
                <div className="w-full overflow-x-auto -mx-2 sm:mx-0">
                  <table className="w-full text-left text-xs min-w-[680px]">
                    <thead>
                      <tr
                        className="border-b font-bold"
                        style={{
                          borderColor: theme.colorSecondary,
                          color: theme.colorText,
                        }}
                      >
                        <th className="py-2.5 px-3 whitespace-nowrap">Pelapor</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Judul & Kategori</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Lokasi</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Urgensi</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Status</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Tanggal</th>
                        <th className="py-2.5 px-3 text-right whitespace-nowrap">Tindakan</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y" style={{ borderColor: `${theme.colorSecondary}40` }}>
                      {list.map(r => (
                        <tr key={r.id} className="hover:bg-black/2 transition">
                          <td className="py-3 px-3">
                            <div className="font-bold">{r.reporter_name}</div>
                            <div className="text-[11px] opacity-75">{r.reporter_room}</div>
                          </td>
                          <td className="py-3 px-3">
                            <div className="font-bold">{r.title}</div>
                            <div className="text-[11px] opacity-75">{r.category}</div>
                          </td>
                          <td className="py-3 px-3">{r.location}</td>
                          <td className="py-3 px-3">{renderUrgencyBadge(r.urgency)}</td>
                          <td className="py-3 px-3">{renderStatusBadge(r.status)}</td>
                          <td className="py-3 px-3 opacity-75">{r.created_at}</td>
                          <td className="py-3 px-3 text-right">
                            <button
                              onClick={() => {
                                setSelectedReportId(r.id);
                                setCurrentPage('report_detail');
                              }}
                              className="px-3.5 py-1 text-white font-bold transition shadow-xs hover:opacity-90"
                              style={{
                                backgroundColor: theme.colorButton,
                                borderRadius: currentBtnRadius,
                              }}
                            >
                              Kelola
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              );
            })()}
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 6. FORMULIR LAPORAN KERUSAKAN */}
      {/* ========================================================================= */}
      {currentPage === 'create_report' && currentUser?.role === 'penghuni' && (
        <main className="flex-1 max-w-3xl mx-auto px-4 py-6 sm:py-8 w-full">
          <div
            className="p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div
              className="flex items-center justify-between pb-4 border-b mb-6"
              style={{ borderColor: `${theme.colorSecondary}80` }}
            >
              <div>
                <h2 className="text-xl font-bold" style={{ color: theme.colorText }}>
                  Buat Laporan Kerusakan
                </h2>
                <p className="text-xs opacity-75 mt-0.5" style={{ color: theme.colorText }}>
                  Tuliskan detail fasilitas kamar yang bermasalah.
                </p>
              </div>
              <button
                onClick={() => setCurrentPage('dashboard')}
                className="px-3 py-1 border text-xs font-semibold hover:opacity-85"
                style={{
                  borderColor: theme.colorSecondary,
                  color: theme.colorText,
                  borderRadius: currentBtnRadius,
                }}
              >
                Kembali
              </button>
            </div>

            <form
              onSubmit={e => {
                e.preventDefault();
                const form = e.currentTarget;
                const title = (form.elements.namedItem('title') as HTMLInputElement).value.trim();
                const category = (form.elements.namedItem('category') as HTMLSelectElement).value;
                const location = (form.elements.namedItem('location') as HTMLInputElement).value.trim();
                const urgency = (form.elements.namedItem('urgency') as HTMLInputElement).value as any;
                const description = (form.elements.namedItem('description') as HTMLTextAreaElement).value.trim();
                const photoInput = form.elements.namedItem('photo') as HTMLInputElement;

                const randomHex = Math.random().toString(16).substring(2, 6).toUpperCase();
                const ticketNum = `KC-202610-${randomHex}`;

                const newReport: ReportItem = {
                  id: reports.length + 1,
                  ticket_number: ticketNum,
                  user_id: currentUser.id,
                  reporter_name: currentUser.name,
                  reporter_room: currentUser.room_number || 'Kamar 102',
                  reporter_phone: currentUser.phone,
                  reporter_email: currentUser.email,
                  title,
                  category,
                  location,
                  description,
                  urgency,
                  photo: photoInput?.files?.[0] ? URL.createObjectURL(photoInput.files[0]) : undefined,
                  status: 'Menunggu Pemeriksaan',
                  created_at: new Date().toISOString().substring(0, 10),
                  history: [
                    {
                      changer_name: currentUser.name,
                      changer_role: 'penghuni',
                      previous_status: 'Baru Dibuat',
                      new_status: 'Menunggu Pemeriksaan',
                      notes: 'Laporan kerusakan fasilitas telah diajukan.',
                      created_at: 'Hari ini',
                    },
                  ],
                  comments: [],
                };

                setReports([newReport, ...reports]);
                setSelectedReportId(newReport.id);
                setCurrentPage('report_detail');
                showNotification('Laporan Anda berhasil dikirimkan.');
              }}
              className="space-y-4 text-xs"
            >
              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Judul Laporan *
                </label>
                <input
                  name="title"
                  required
                  placeholder="Contoh: Kran Air Wastafel Bocor"
                  className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Kategori Kerusakan *
                  </label>
                  <select
                    name="category"
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  >
                    <option value="">Pilih Kategori</option>
                    <option value="Listrik">Listrik</option>
                    <option value="Air dan sanitasi">Air dan sanitasi</option>
                    <option value="Furnitur">Furnitur</option>
                    <option value="Bangunan">Bangunan</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Lainnya">Lainnya</option>
                  </select>
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Lokasi Kerusakan *
                  </label>
                  <input
                    name="location"
                    required
                    defaultValue={currentUser.room_number || 'Kamar 102'}
                    placeholder="Contoh: Kamar 102"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>
              </div>

              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Tingkat Urgensi *
                </label>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  {['Rendah', 'Sedang', 'Tinggi', 'Darurat'].map((u, i) => (
                    <label
                      key={u}
                      className="p-3 rounded-xl border cursor-pointer flex items-center justify-center gap-2 transition hover:opacity-85"
                      style={{
                        backgroundColor: theme.colorCard,
                        borderColor: theme.colorSecondary,
                      }}
                    >
                      <input
                        type="radio"
                        name="urgency"
                        value={u}
                        defaultChecked={i === 1}
                        style={{ accentColor: theme.colorButton }}
                      />
                      <span className="font-bold text-xs" style={{ color: theme.colorText }}>
                        {u}
                      </span>
                    </label>
                  ))}
                </div>
              </div>

              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Deskripsi Kerusakan *
                </label>
                <textarea
                  name="description"
                  required
                  rows={4}
                  placeholder="Jelaskan kendala kerusakan yang terjadi secara jelas..."
                  className="w-full px-3.5 py-2.5 rounded-xl border text-sm focus:outline-none"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
              </div>

              <div>
                <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                  Foto Kerusakan (Opsional)
                </label>
                <input
                  type="file"
                  name="photo"
                  accept="image/*"
                  className="w-full px-3 py-2 rounded-xl border text-xs"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                  }}
                />
                <span className="text-[11px] opacity-75 mt-1 block" style={{ color: theme.colorText }}>
                  Format: JPG, JPEG, PNG, WebP (maksimal 2 MB).
                </span>
              </div>

              <div className="pt-4 flex justify-end gap-2 border-t" style={{ borderColor: `${theme.colorSecondary}80` }}>
                <button
                  type="button"
                  onClick={() => setCurrentPage('dashboard')}
                  className="px-4 py-2 border text-xs font-semibold hover:opacity-85"
                  style={{
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-6 py-2.5 text-white font-bold text-xs shadow-xs hover:opacity-95"
                  style={{
                    backgroundColor: theme.colorButton,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Kirim Laporan
                </button>
              </div>
            </form>
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 7. DETAIL LAPORAN & RIWAYAT STATUS */}
      {/* ========================================================================= */}
      {currentPage === 'report_detail' && currentUser && selectedReportId && (() => {
        const report = reports.find(r => r.id === selectedReportId);
        if (!report) return null;
        const isManager = currentUser.role === 'pengelola';

        return (
          <main className="flex-1 max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8 w-full">
            <div className="mb-4">
              <button
                onClick={() => setCurrentPage('dashboard')}
                className="text-xs font-semibold flex items-center gap-1 hover:opacity-80"
                style={{ color: theme.colorText }}
              >
                <ArrowLeft className="w-3.5 h-3.5" style={{ color: theme.colorButton }} /> Kembali ke Dashboard
              </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              {/* Kolom Kiri: Detail Laporan, Foto, Timeline, Komentar */}
              <div className="lg:col-span-8 space-y-6">
                <div
                  className="p-6 sm:p-8 border shadow-sm"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <div
                    className="flex flex-wrap justify-between items-start gap-2 pb-4 border-b mb-4"
                    style={{ borderColor: `${theme.colorSecondary}80` }}
                  >
                    <div>
                      <span className="text-[11px] opacity-75 block" style={{ color: theme.colorText }}>
                        No. Laporan: {report.ticket_number}
                      </span>
                      <h2 className="text-xl font-bold mt-0.5" style={{ color: theme.colorText }}>
                        {report.title}
                      </h2>
                    </div>
                    <div className="flex items-center gap-2">
                      {renderUrgencyBadge(report.urgency)}
                      {renderStatusBadge(report.status)}
                    </div>
                  </div>

                  <div
                    className="grid grid-cols-2 sm:grid-cols-4 gap-3 p-3.5 rounded-2xl text-xs mb-4"
                    style={{ backgroundColor: `${theme.colorSecondary}50` }}
                  >
                    <div>
                      <span className="text-[10px] opacity-75 block" style={{ color: theme.colorText }}>Kategori</span>
                      <span className="font-bold" style={{ color: theme.colorText }}>{report.category}</span>
                    </div>
                    <div>
                      <span className="text-[10px] opacity-75 block" style={{ color: theme.colorText }}>Lokasi</span>
                      <span className="font-bold" style={{ color: theme.colorText }}>{report.location}</span>
                    </div>
                    <div>
                      <span className="text-[10px] opacity-75 block" style={{ color: theme.colorText }}>Pelapor</span>
                      <span className="font-bold" style={{ color: theme.colorText }}>{report.reporter_name}</span>
                    </div>
                    <div>
                      <span className="text-[10px] opacity-75 block" style={{ color: theme.colorText }}>Tanggal</span>
                      <span className="font-bold" style={{ color: theme.colorText }}>{report.created_at}</span>
                    </div>
                  </div>

                  <div className="mb-4">
                    <label className="block text-xs font-bold mb-1" style={{ color: theme.colorText }}>
                      Deskripsi Kerusakan:
                    </label>
                    <div
                      className="p-3.5 border rounded-xl text-xs leading-relaxed whitespace-pre-wrap"
                      style={{
                        backgroundColor: theme.colorCard,
                        borderColor: theme.colorSecondary,
                        color: theme.colorText,
                      }}
                    >
                      {report.description}
                    </div>
                  </div>

                  {report.photo && (
                    <div className="mb-4">
                      <label className="block text-xs font-bold mb-1" style={{ color: theme.colorText }}>
                        Foto Bukti Kerusakan:
                      </label>
                      <div
                        className="max-w-md rounded-2xl overflow-hidden border p-1"
                        style={{
                          borderColor: theme.colorSecondary,
                          backgroundColor: theme.colorCard,
                        }}
                      >
                        <img
                          src={report.photo}
                          alt="Foto Kerusakan"
                          className="w-full max-h-60 object-cover rounded-xl"
                        />
                      </div>
                    </div>
                  )}

                  {/* Hasil Perbaikan Selesai */}
                  {report.status === 'Selesai' && report.repair_record && (
                    <div className="p-4 rounded-2xl bg-emerald-50 text-emerald-800 text-xs mb-4 border border-emerald-200">
                      <h4 className="font-bold text-sm mb-1">Hasil Perbaikan Selesai</h4>
                      <p className="mb-0.5"><strong>Teknisi:</strong> {report.repair_record.technician_name}</p>
                      <p className="mb-0.5"><strong>Tanggal Selesai:</strong> {report.repair_record.completion_date}</p>
                      <p className="mb-0.5"><strong>Catatan:</strong> {report.repair_record.repair_notes}</p>
                      {report.repair_record.cost_estimate > 0 && (
                        <p className="mb-0"><strong>Biaya:</strong> Rp {report.repair_record.cost_estimate.toLocaleString('id-ID')}</p>
                      )}
                    </div>
                  )}
                </div>

                {/* Timeline Riwayat Status */}
                <div
                  className="p-6 sm:p-8 border shadow-sm"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <h3
                    className="font-bold text-base mb-4 pb-2 border-b"
                    style={{
                      color: theme.colorText,
                      borderColor: `${theme.colorSecondary}80`,
                    }}
                  >
                    Riwayat Perkembangan Laporan
                  </h3>

                  <div
                    className="relative pl-6 space-y-5 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5"
                    style={{ '--tw-before-bg': theme.colorSecondary } as any}
                  >
                    {report.history.map((h, idx) => (
                      <div key={idx} className="relative">
                        <span
                          className="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full border-2"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorButton,
                          }}
                        />
                        <div className="flex items-center gap-2 mb-0.5">
                          {renderStatusBadge(h.new_status)}
                          <span className="text-[11px] opacity-75">{h.created_at}</span>
                        </div>
                        <span className="text-xs font-bold" style={{ color: theme.colorText }}>
                          Oleh {h.changer_name} ({h.changer_role})
                        </span>
                        {h.notes && (
                          <p
                            className="text-xs mt-1 p-2.5 rounded-xl border"
                            style={{
                              backgroundColor: `${theme.colorSecondary}30`,
                              borderColor: `${theme.colorSecondary}80`,
                              color: theme.colorText,
                            }}
                          >
                            {h.notes}
                          </p>
                        )}
                      </div>
                    ))}
                  </div>
                </div>

                {/* Diskusi & Tanggapan */}
                <div
                  className="p-6 sm:p-8 border shadow-sm"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <h3
                    className="font-bold text-base mb-3 pb-2 border-b"
                    style={{
                      color: theme.colorText,
                      borderColor: `${theme.colorSecondary}80`,
                    }}
                  >
                    Tanggapan & Informasi
                  </h3>

                  <div className="space-y-3 mb-4">
                    {report.comments.length === 0 ? (
                      <p className="text-xs opacity-75" style={{ color: theme.colorText }}>
                        Belum ada tanggapan untuk laporan ini.
                      </p>
                    ) : (
                      report.comments.map((c, i) => (
                        <div
                          key={i}
                          className="p-3 rounded-2xl text-xs border"
                          style={{
                            backgroundColor: c.author_role === 'pengelola' ? `${theme.colorSecondary}40` : theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        >
                          <div className="flex justify-between items-center mb-1">
                            <span className="font-bold">{c.author_name} ({c.author_role})</span>
                            <span className="text-[10px] opacity-75">{c.created_at}</span>
                          </div>
                          <p className="whitespace-pre-wrap mb-0">{c.comment}</p>
                        </div>
                      ))
                    )}
                  </div>

                  <form
                    onSubmit={e => {
                      e.preventDefault();
                      const input = e.currentTarget.elements.namedItem('comm_text') as HTMLInputElement;
                      if (!input.value.trim()) return;

                      const newC = {
                        author_name: currentUser.name,
                        author_role: currentUser.role,
                        comment: input.value.trim(),
                        created_at: 'Hari ini',
                      };

                      setReports(
                        reports.map(r => (r.id === report.id ? { ...r, comments: [...r.comments, newC] } : r))
                      );
                      input.value = '';
                      showNotification('Tanggapan terkirim.');
                    }}
                    className="flex gap-2"
                  >
                    <input
                      name="comm_text"
                      required
                      placeholder="Tulis pesan atau tanggapan..."
                      className="flex-1 px-3.5 py-2 rounded-full border text-xs focus:outline-none"
                      style={{
                        backgroundColor: theme.colorCard,
                        borderColor: theme.colorSecondary,
                        color: theme.colorText,
                      }}
                    />
                    <button
                      type="submit"
                      className="px-4 py-2 text-white text-xs font-bold shadow-xs hover:opacity-90"
                      style={{
                        backgroundColor: theme.colorButton,
                        borderRadius: currentBtnRadius,
                      }}
                    >
                      Kirim
                    </button>
                  </form>
                </div>
              </div>

              {/* Kolom Kanan: Ubah Status (Pengelola) & Info Kontak */}
              <div className="lg:col-span-4 space-y-6">
                {isManager && (
                  <div
                    className="p-6 border shadow-sm"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      borderRadius: theme.cardRadius,
                    }}
                  >
                    <h3
                      className="font-bold text-sm mb-3 pb-2 border-b"
                      style={{
                        color: theme.colorText,
                        borderColor: `${theme.colorSecondary}80`,
                      }}
                    >
                      Ubah Status Laporan
                    </h3>

                    <form
                      onSubmit={e => {
                        e.preventDefault();
                        const form = e.currentTarget;
                        const newStatus = (form.elements.namedItem('status_val') as HTMLSelectElement).value as any;
                        const notes = (form.elements.namedItem('status_notes') as HTMLTextAreaElement).value.trim();
                        const tech = (form.elements.namedItem('status_tech') as HTMLInputElement)?.value;
                        const cdate = (form.elements.namedItem('status_cdate') as HTMLInputElement)?.value;
                        const cost = parseFloat((form.elements.namedItem('status_cost') as HTMLInputElement)?.value || '0');

                        if (newStatus === 'Selesai' && !notes) {
                          showNotification('Status Selesai wajib menyertakan catatan hasil perbaikan.');
                          return;
                        }

                        const newHistory = {
                          changer_name: currentUser.name,
                          changer_role: 'pengelola',
                          previous_status: report.status,
                          new_status: newStatus,
                          notes: notes || 'Status diperbarui oleh pengelola.',
                          created_at: 'Hari ini',
                        };

                        setReports(
                          reports.map(r =>
                            r.id === report.id
                              ? {
                                  ...r,
                                  status: newStatus,
                                  history: [...r.history, newHistory],
                                  repair_record:
                                    newStatus === 'Selesai'
                                      ? {
                                          technician_name: tech || 'Pak Joko Santoso',
                                          completion_date: cdate || new Date().toISOString().substring(0, 10),
                                          repair_notes: notes,
                                          cost_estimate: cost || 0,
                                        }
                                      : r.repair_record,
                                }
                              : r
                          )
                        );
                        showNotification(`Status berhasil diubah menjadi ${newStatus}.`);
                      }}
                      className="space-y-3 text-xs"
                    >
                      <div>
                        <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                          Status Baru *
                        </label>
                        <select
                          name="status_val"
                          defaultValue={report.status}
                          className="w-full px-3 py-2 rounded-xl border focus:outline-none"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        >
                          <option value="Menunggu Pemeriksaan">Menunggu Pemeriksaan</option>
                          <option value="Sedang Diproses">Sedang Diproses</option>
                          <option value="Menunggu Informasi">Menunggu Informasi</option>
                          <option value="Selesai">Selesai</option>
                          <option value="Ditolak">Ditolak</option>
                        </select>
                      </div>

                      <div className="p-3 rounded-xl border space-y-2" style={{ backgroundColor: `${theme.colorSecondary}30`, borderColor: theme.colorSecondary }}>
                        <span className="block font-semibold text-[11px]" style={{ color: theme.colorText }}>
                          Khusus Status Selesai:
                        </span>
                        <div>
                          <label className="block text-[10px] opacity-75">Nama Teknisi</label>
                          <input
                            name="status_tech"
                            defaultValue="Pak Joko Santoso"
                            className="w-full px-2.5 py-1.5 rounded-lg border text-xs"
                            style={{
                              backgroundColor: theme.colorCard,
                              borderColor: theme.colorSecondary,
                              color: theme.colorText,
                            }}
                          />
                        </div>
                        <div>
                          <label className="block text-[10px] opacity-75">Estimasi Biaya (Rp)</label>
                          <input
                            type="number"
                            name="status_cost"
                            defaultValue="0"
                            className="w-full px-2.5 py-1.5 rounded-lg border text-xs"
                            style={{
                              backgroundColor: theme.colorCard,
                              borderColor: theme.colorSecondary,
                              color: theme.colorText,
                            }}
                          />
                        </div>
                      </div>

                      <div>
                        <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                          Catatan / Keterangan
                        </label>
                        <textarea
                          name="status_notes"
                          rows={3}
                          placeholder="Tambahkan catatan perubahan status..."
                          className="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        />
                      </div>

                      <button
                        type="submit"
                        className="w-full py-2.5 text-white font-bold text-xs shadow-xs hover:opacity-95"
                        style={{
                          backgroundColor: theme.colorButton,
                          borderRadius: currentBtnRadius,
                        }}
                      >
                        Simpan Perubahan
                      </button>
                    </form>
                  </div>
                )}

                <div
                  className="p-6 border shadow-sm text-xs space-y-2.5"
                  style={{
                    backgroundColor: theme.colorCard,
                    borderColor: theme.colorSecondary,
                    borderRadius: theme.cardRadius,
                  }}
                >
                  <h3
                    className="font-bold text-sm pb-2 border-b"
                    style={{
                      color: theme.colorText,
                      borderColor: `${theme.colorSecondary}80`,
                    }}
                  >
                    Informasi Penghuni
                  </h3>
                  <div>
                    <span className="text-[10px] opacity-75 block">Nama Pelapor</span>
                    <strong className="text-sm" style={{ color: theme.colorText }}>{report.reporter_name}</strong>
                  </div>
                  <div>
                    <span className="text-[10px] opacity-75 block">Kamar</span>
                    <span style={{ color: theme.colorText }}>{report.reporter_room}</span>
                  </div>
                  <div>
                    <span className="text-[10px] opacity-75 block">No. Telepon / WA</span>
                    <span style={{ color: theme.colorText }}>{report.reporter_phone}</span>
                  </div>
                  <div>
                    <span className="text-[10px] opacity-75 block">Email</span>
                    <span style={{ color: theme.colorText }}>{report.reporter_email}</span>
                  </div>
                </div>
              </div>
            </div>
          </main>
        );
      })()}

      {/* ========================================================================= */}
      {/* 8. RIWAYAT PERBAIKAN SELESAI */}
      {/* ========================================================================= */}
      {currentPage === 'repair_history' && currentUser && (
        <main className="flex-1 max-w-6xl mx-auto px-4 sm:px-6 py-6 sm:py-8 w-full">
          <div
            className="p-6 sm:p-8 border shadow-sm"
            style={{
              backgroundColor: theme.colorCard,
              borderColor: theme.colorSecondary,
              borderRadius: theme.cardRadius,
            }}
          >
            <div
              className="flex justify-between items-center pb-4 border-b mb-4"
              style={{ borderColor: `${theme.colorSecondary}80` }}
            >
              <div>
                <h2 className="text-xl font-bold" style={{ color: theme.colorText }}>
                  Riwayat Perbaikan Selesai
                </h2>
                <p className="text-xs opacity-75" style={{ color: theme.colorText }}>
                  Dokumentasi perbaikan fasilitas kos yang telah tuntas dikerjakan.
                </p>
              </div>
              <button
                onClick={() => setCurrentPage('dashboard')}
                className="px-3.5 py-1 border text-xs font-semibold hover:opacity-85"
                style={{
                  borderColor: theme.colorSecondary,
                  color: theme.colorText,
                  borderRadius: currentBtnRadius,
                }}
              >
                Kembali
              </button>
            </div>

            {(() => {
              const completedList = reports.filter(r => r.status === 'Selesai');

              if (completedList.length === 0) {
                return (
                  <div className="text-center py-12 text-xs opacity-75" style={{ color: theme.colorText }}>
                    Belum ada fasilitas yang tercatat selesai diperbaiki.
                  </div>
                );
              }

              return (
                <div className="w-full overflow-x-auto -mx-2 sm:mx-0">
                  <table className="w-full text-left text-xs min-w-[680px]">
                    <thead>
                      <tr
                        className="border-b font-bold"
                        style={{
                          borderColor: theme.colorSecondary,
                          color: theme.colorText,
                        }}
                      >
                        <th className="py-2.5 px-3 whitespace-nowrap">Kerusakan & Lokasi</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Pelapor</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Tanggal Selesai</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Teknisi & Catatan</th>
                        <th className="py-2.5 px-3 whitespace-nowrap">Biaya</th>
                        <th className="py-2.5 px-3 text-right whitespace-nowrap">Rincian</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y" style={{ borderColor: `${theme.colorSecondary}40` }}>
                      {completedList.map(r => (
                        <tr key={r.id} className="hover:bg-black/2 transition">
                          <td className="py-3 px-3">
                            <div className="font-bold">{r.title}</div>
                            <div className="text-[11px] opacity-75">{r.location}</div>
                          </td>
                          <td className="py-3 px-3">
                            <div className="font-bold">{r.reporter_name}</div>
                            <div className="text-[11px] opacity-75">{r.reporter_room}</div>
                          </td>
                          <td className="py-3 px-3">
                            {r.repair_record?.completion_date || r.created_at}
                          </td>
                          <td className="py-3 px-3 max-w-xs">
                            <div className="font-bold text-emerald-800">
                              {r.repair_record?.technician_name || 'Tim Perbaikan'}
                            </div>
                            <div className="text-[11px] opacity-75 truncate">
                              {r.repair_record?.repair_notes || '-'}
                            </div>
                          </td>
                          <td className="py-3 px-3 font-semibold">
                            {r.repair_record?.cost_estimate
                              ? `Rp ${r.repair_record.cost_estimate.toLocaleString('id-ID')}`
                              : '-'}
                          </td>
                          <td className="py-3 px-3 text-right">
                            <button
                              onClick={() => {
                                setSelectedReportId(r.id);
                                setCurrentPage('report_detail');
                              }}
                              className="px-3.5 py-1 border font-semibold hover:opacity-85"
                              style={{
                                borderColor: theme.colorSecondary,
                                color: theme.colorText,
                                borderRadius: currentBtnRadius,
                              }}
                            >
                              Lihat
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              );
            })()}
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 9. PROFIL PENGGUNA & GANTI KATA SANDI */}
      {/* ========================================================================= */}
      {currentPage === 'profile' && currentUser && (
        <main className="flex-1 max-w-4xl mx-auto px-4 sm:px-6 py-6 sm:py-8 w-full">
          <div className="mb-4">
            <button
              onClick={() => setCurrentPage('dashboard')}
              className="text-xs font-semibold flex items-center gap-1 hover:opacity-80"
              style={{ color: theme.colorText }}
            >
              <ArrowLeft className="w-3.5 h-3.5" style={{ color: theme.colorButton }} /> Kembali ke Dashboard
            </button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              className="p-6 sm:p-8 border shadow-sm"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                borderRadius: theme.cardRadius,
              }}
            >
              <h3
                className="font-bold text-base mb-4 pb-2 border-b flex items-center gap-2"
                style={{
                  color: theme.colorText,
                  borderColor: `${theme.colorSecondary}80`,
                }}
              >
                <User className="w-4 h-4" style={{ color: theme.colorButton }} />
                <span>Informasi Profil</span>
              </h3>

              <form
                onSubmit={e => {
                  e.preventDefault();
                  const form = e.currentTarget;
                  const name = (form.elements.namedItem('prof_name') as HTMLInputElement).value.trim();
                  const email = (form.elements.namedItem('prof_email') as HTMLInputElement).value.trim();
                  const phone = (form.elements.namedItem('prof_phone') as HTMLInputElement).value.trim();
                  const room = (form.elements.namedItem('prof_room') as HTMLInputElement).value.trim();

                  const updated = {
                    ...currentUser,
                    name,
                    email,
                    phone,
                    room_number: room,
                  };

                  setCurrentUser(updated);
                  setUsers(users.map(u => (u.id === currentUser.id ? updated : u)));
                  showNotification('Profil berhasil disimpan.');
                }}
                className="space-y-3.5 text-xs"
              >
                <div>
                  <label className="block text-[10px] opacity-75 mb-1">Username</label>
                  <input
                    value={currentUser.username}
                    disabled
                    className="w-full px-3 py-2 rounded-xl border opacity-60 text-xs"
                    style={{
                      backgroundColor: `${theme.colorSecondary}20`,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Nama Lengkap *
                  </label>
                  <input
                    name="prof_name"
                    defaultValue={currentUser.name}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Email *
                  </label>
                  <input
                    type="email"
                    name="prof_email"
                    defaultValue={currentUser.email}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    No. WhatsApp / Telepon *
                  </label>
                  <input
                    name="prof_phone"
                    defaultValue={currentUser.phone}
                    required
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Nomor Kamar
                  </label>
                  <input
                    name="prof_room"
                    defaultValue={currentUser.room_number}
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <button
                  type="submit"
                  className="w-full py-2.5 text-white font-bold text-xs shadow-xs hover:opacity-95 mt-2"
                  style={{
                    backgroundColor: theme.colorButton,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Simpan Perubahan
                </button>
              </form>
            </div>

            <div
              className="p-6 sm:p-8 border shadow-sm"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                borderRadius: theme.cardRadius,
              }}
            >
              <h3
                className="font-bold text-base mb-4 pb-2 border-b flex items-center gap-2"
                style={{
                  color: theme.colorText,
                  borderColor: `${theme.colorSecondary}80`,
                }}
              >
                <KeyRound className="w-4 h-4" style={{ color: theme.colorButton }} />
                <span>Ganti Kata Sandi</span>
              </h3>

              <form
                onSubmit={e => {
                  e.preventDefault();
                  const form = e.currentTarget;
                  const newp = (form.elements.namedItem('new_pass') as HTMLInputElement).value;
                  const confp = (form.elements.namedItem('conf_pass') as HTMLInputElement).value;

                  if (newp !== confp) {
                    showNotification('Konfirmasi kata sandi baru tidak cocok.');
                    return;
                  }

                  form.reset();
                  showNotification('Kata sandi berhasil diperbarui.');
                }}
                className="space-y-3.5 text-xs"
              >
                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Kata Sandi Lama *
                  </label>
                  <input
                    type="password"
                    name="old_pass"
                    required
                    placeholder="Masukkan kata sandi lama"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Kata Sandi Baru (Min 8 Karakter) *
                  </label>
                  <input
                    type="password"
                    name="new_pass"
                    required
                    minLength={8}
                    placeholder="Minimal 8 karakter"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <div>
                  <label className="block font-bold mb-1" style={{ color: theme.colorText }}>
                    Ulangi Kata Sandi Baru *
                  </label>
                  <input
                    type="password"
                    name="conf_pass"
                    required
                    minLength={8}
                    placeholder="Ketik ulang kata sandi baru"
                    className="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                    }}
                  />
                </div>

                <button
                  type="submit"
                  className="w-full py-2.5 border font-bold text-xs transition hover:opacity-85 mt-2"
                  style={{
                    borderColor: theme.colorSecondary,
                    color: theme.colorText,
                    borderRadius: currentBtnRadius,
                  }}
                >
                  Perbarui Kata Sandi
                </button>
              </form>
            </div>
          </div>
        </main>
      )}

      {/* ========================================================================= */}
      {/* 10. PENGATURAN TAMPILAN & KUSTOMISASI WEBSITE (KHUSUS PENGELOLA) */}
      {/* ========================================================================= */}
      {currentPage === 'theme_settings' && (
        <main className="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 py-6 sm:py-8">
          {currentUser?.role !== 'pengelola' ? (
            <div
              className="max-w-md mx-auto my-12 p-8 border text-center shadow-sm"
              style={{
                backgroundColor: theme.colorCard,
                borderColor: theme.colorSecondary,
                borderRadius: theme.cardRadius,
              }}
            >
              <div className="w-12 h-12 rounded-full bg-red-100 text-red-700 flex items-center justify-center mx-auto mb-3">
                <AlertCircle className="w-6 h-6" />
              </div>
              <h3 className="font-bold text-base mb-1" style={{ color: theme.colorText }}>Akses Terbatas</h3>
              <p className="text-xs opacity-75 mb-4" style={{ color: theme.colorText }}>
                Halaman Pengaturan Tampilan hanya dapat diakses oleh akun Pengelola KosCare.
              </p>
              <button
                onClick={() => setCurrentPage(currentUser ? 'dashboard' : 'home')}
                className="px-5 py-2.5 text-white text-xs font-bold shadow-xs hover:opacity-90"
                style={{
                  backgroundColor: theme.colorButton,
                  borderRadius: currentBtnRadius,
                }}
              >
                Kembali ke Dashboard
              </button>
            </div>
          ) : (
            <div>
              {/* Top Bar Header */}
              <div className="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                  <h2 className="text-2xl font-bold tracking-tight" style={{ color: theme.colorText }}>
                    Pengaturan Tampilan
                  </h2>
                  <p className="text-xs opacity-75 mt-0.5" style={{ color: theme.colorText }}>
                    Kustomisasi palet warna, tipografi global, identitas merek, dan bentuk tampilan website.
                  </p>
                </div>

                <div className="flex items-center gap-2">
                  <button
                    onClick={() => {
                      if (window.confirm('Kembalikan semua pengaturan tampilan ke setelan bawaan (Pastel Lilac & Times New Roman)?')) {
                        handleResetTheme();
                      }
                    }}
                    className="flex items-center gap-1.5 px-3.5 py-2 border text-xs font-semibold hover:opacity-85 transition shadow-2xs"
                    style={{
                      borderColor: theme.colorSecondary,
                      color: theme.colorText,
                      backgroundColor: theme.colorCard,
                      borderRadius: currentBtnRadius,
                    }}
                  >
                    <RotateCcw className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                    <span>Kembalikan ke Default</span>
                  </button>
                </div>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                {/* Kolom Kiri: Form Kontrol (7 Kolom) */}
                <div className="lg:col-span-7 space-y-5">
                  {/* Bagian A: Tema Warna Siap Pakai & Color Picker */}
                  <div
                    className="p-5 sm:p-6 border shadow-xs"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      borderRadius: theme.cardRadius,
                    }}
                  >
                    <div className="flex items-center gap-2 mb-2">
                      <span
                        className="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        style={{ backgroundColor: theme.colorSecondary, color: theme.colorButton }}
                      >
                        <Palette className="w-4 h-4" />
                      </span>
                      <h3 className="font-bold text-sm" style={{ color: theme.colorText }}>
                        Tema Warna Siap Pakai (Termasuk Baby Blue)
                      </h3>
                    </div>
                    <p className="text-xs opacity-75 mb-4" style={{ color: theme.colorText }}>
                      Pilih kombinasi palet pastel yang diselaraskan secara profesional atau sesuaikan warna secara manual.
                    </p>

                    {/* Presets Grid */}
                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5 mb-5">
                      {Object.entries(THEME_PRESETS).map(([pName, pColors]) => {
                        const isActive = draftTheme.presetName === pName;
                        return (
                          <button
                            key={pName}
                            type="button"
                            onClick={() => {
                              setDraftTheme({
                                ...draftTheme,
                                presetName: pName,
                                colorPrimary: pColors.primary,
                                colorSecondary: pColors.secondary,
                                colorBackground: pColors.background,
                                colorCard: pColors.card,
                                colorButton: pColors.button,
                                colorText: pColors.text,
                              });
                            }}
                            className="p-3 rounded-2xl border text-left transition relative cursor-pointer"
                            style={{
                              borderColor: isActive ? theme.colorButton : theme.colorSecondary,
                              backgroundColor: isActive ? `${pColors.secondary}60` : theme.colorCard,
                              boxShadow: isActive ? `0 0 0 2px ${theme.colorButton}` : 'none',
                            }}
                          >
                            <div className="flex items-center justify-between mb-2">
                              <span className="font-bold text-xs" style={{ color: pColors.text }}>{pName}</span>
                              {isActive && <Check className="w-3.5 h-3.5" style={{ color: pColors.button }} />}
                            </div>
                            <div className="flex items-center gap-1.5">
                              <span className="w-4 h-4 rounded-full border border-black/10" style={{ backgroundColor: pColors.primary }} title="Primer" />
                              <span className="w-4 h-4 rounded-full border border-black/10" style={{ backgroundColor: pColors.secondary }} title="Sekunder" />
                              <span className="w-4 h-4 rounded-full border border-black/10" style={{ backgroundColor: pColors.background }} title="Latar" />
                              <span className="w-4 h-4 rounded-full border border-black/10" style={{ backgroundColor: pColors.button }} title="Tombol" />
                            </div>
                          </button>
                        );
                      })}

                      {/* Custom indicator */}
                      <button
                        type="button"
                        onClick={() => setDraftTheme({ ...draftTheme, presetName: 'Custom' })}
                        className="p-3 rounded-2xl border text-left transition cursor-pointer"
                        style={{
                          borderColor: draftTheme.presetName === 'Custom' ? theme.colorButton : theme.colorSecondary,
                          backgroundColor: draftTheme.presetName === 'Custom' ? `${theme.colorSecondary}60` : theme.colorCard,
                          boxShadow: draftTheme.presetName === 'Custom' ? `0 0 0 2px ${theme.colorButton}` : 'none',
                        }}
                      >
                        <div className="flex items-center justify-between mb-2">
                          <span className="font-bold text-xs" style={{ color: theme.colorText }}>Custom</span>
                          {draftTheme.presetName === 'Custom' && <Check className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />}
                        </div>
                        <p className="text-[10px] opacity-75">Atur palet bebas</p>
                      </button>
                    </div>

                    <div className="border-t pt-4" style={{ borderColor: `${theme.colorSecondary}80` }}>
                      <h4 className="font-bold text-xs mb-3" style={{ color: theme.colorText }}>Detail Color Picker</h4>
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Tombol Utama</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorButton}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorButton: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorButton.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorButton: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Utama (Aksen Primer)</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorPrimary}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorPrimary: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorPrimary.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorPrimary: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Sekunder / Badge</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorSecondary}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorSecondary: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorSecondary.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorSecondary: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Latar Belakang</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorBackground}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorBackground: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorBackground.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorBackground: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Kartu Konten</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorCard}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorCard: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorCard.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorCard: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>

                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>Warna Teks Utama</label>
                          <div className="flex items-center gap-2">
                            <input
                              type="color"
                              value={draftTheme.colorText}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorText: e.target.value })}
                              className="w-9 h-8 p-0.5 rounded-lg border border-gray-300 cursor-pointer"
                            />
                            <input
                              type="text"
                              value={draftTheme.colorText.toUpperCase()}
                              onChange={e => setDraftTheme({ ...draftTheme, presetName: 'Custom', colorText: e.target.value })}
                              className="w-full px-2.5 py-1.5 rounded-xl border text-xs uppercase"
                              style={{ borderColor: theme.colorSecondary, backgroundColor: theme.colorCard, color: theme.colorText }}
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Bagian B: Tipografi Global & CONTOH TULISAN LANGSUNG */}
                  <div
                    className="p-5 sm:p-6 border shadow-xs"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      borderRadius: theme.cardRadius,
                    }}
                  >
                    <div className="flex items-center gap-2 mb-2">
                      <span
                        className="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        style={{ backgroundColor: theme.colorSecondary, color: theme.colorButton }}
                      >
                        <Type className="w-4 h-4" />
                      </span>
                      <h3 className="font-bold text-sm" style={{ color: theme.colorText }}>
                        Pengaturan Font Global & Contoh Tulisan Langsung
                      </h3>
                    </div>
                    <p className="text-xs opacity-75 mb-4" style={{ color: theme.colorText }}>
                      Font yang dipilih akan diterapkan secara serentak ke seluruh halaman: landing page, dashboard penghuni, dashboard pengelola, formulir, tabel, tombol, dan navigasi.
                    </p>

                    {/* Kartu Pilihan Font Langsung */}
                    <label className="block font-semibold text-xs mb-2" style={{ color: theme.colorText }}>
                      Pilih Jenis Font:
                    </label>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mb-4">
                      {AVAILABLE_FONTS.map(f => {
                        const isFontSelected = draftTheme.fontFamily === f.id;
                        return (
                          <div
                            key={f.id}
                            onClick={() => setDraftTheme({ ...draftTheme, fontFamily: f.id })}
                            className="p-3 rounded-2xl border cursor-pointer transition text-left"
                            style={{
                              fontFamily: f.id,
                              borderColor: isFontSelected ? theme.colorButton : theme.colorSecondary,
                              backgroundColor: isFontSelected ? `${theme.colorSecondary}60` : theme.colorCard,
                              boxShadow: isFontSelected ? `0 0 0 2px ${theme.colorButton}` : 'none',
                            }}
                          >
                            <div className="flex items-center justify-between mb-1">
                              <span className="font-bold text-xs" style={{ color: theme.colorText }}>{f.name}</span>
                              {isFontSelected && <Check className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />}
                            </div>
                            <span className="text-[11px] opacity-75 block" style={{ color: theme.colorText }}>
                              {f.desc}
                            </span>
                            <div className="text-[11px] mt-1.5 opacity-90 font-medium" style={{ color: theme.colorButton }}>
                              Aa Bb Gg 12345
                            </div>
                          </div>
                        );
                      })}
                    </div>

                    {/* KOTAK CONTOH TULISAN LANGSUNG (LIVE FONT PREVIEW BOX) */}
                    <div
                      className="p-4 rounded-2xl border mb-4"
                      style={{
                        backgroundColor: `${theme.colorBackground}`,
                        borderColor: theme.colorSecondary,
                        fontFamily: draftTheme.fontFamily,
                      }}
                    >
                      <div className="flex items-center justify-between mb-2">
                        <span className="font-bold text-xs" style={{ color: theme.colorText }}>
                          Contoh Tulisan Langsung (Live Typography):
                        </span>
                        <span
                          className="px-2 py-0.5 rounded-full text-[10px] font-bold"
                          style={{
                            backgroundColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        >
                          {AVAILABLE_FONTS.find(f => f.id === draftTheme.fontFamily)?.name || 'Font Aktif'}
                        </span>
                      </div>

                      <div className="space-y-2">
                        <h4 className="text-base font-bold leading-tight" style={{ color: theme.colorText }}>
                          Perawatan Fasilitas Kos Jadi Lebih Mudah
                        </h4>
                        <p className="text-xs leading-relaxed opacity-85" style={{ color: theme.colorText }}>
                          {customFontSample ||
                            'Kamar 102 — Kran wastafel air menetes terus menerus sejak pagi. Laporan telah diverifikasi oleh pengelola dan teknisi siap menangani perbaikan.'}
                        </p>
                        <div className="flex flex-wrap items-center gap-2 pt-1">
                          <span
                            className="px-2.5 py-0.5 rounded-full text-[10px] font-semibold"
                            style={{ backgroundColor: theme.colorSecondary, color: theme.colorText }}
                          >
                            Sedang Diproses
                          </span>
                          <span className="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                            Selesai: Rp 125.000
                          </span>
                          <span className="text-[10px] opacity-75" style={{ color: theme.colorText }}>
                            Tiket: <strong>KC-202610-0001</strong>
                          </span>
                        </div>
                      </div>

                      {/* Input Uji Ketik Sendiri */}
                      <div className="mt-3 pt-3 border-t" style={{ borderColor: `${theme.colorSecondary}80` }}>
                        <label className="block text-[11px] font-semibold mb-1" style={{ color: theme.colorText }}>
                          Uji Ketik Kalimat Anda Sendiri:
                        </label>
                        <input
                          type="text"
                          value={customFontSample}
                          onChange={e => setCustomFontSample(e.target.value)}
                          placeholder="Ketik kata atau kalimat untuk menguji font langsung..."
                          className="w-full px-3 py-1.5 rounded-xl border text-xs focus:outline-none"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                            fontFamily: draftTheme.fontFamily,
                          }}
                        />
                      </div>
                    </div>

                    <div className="text-xs">
                      <label className="block font-semibold mb-1.5" style={{ color: theme.colorText }}>
                        Skala Ukuran Teks
                      </label>
                      <div className="flex items-center gap-4">
                        {[
                          { id: 'compact', label: 'Ringkas (95%)' },
                          { id: 'normal', label: 'Normal (100% - Bawaan)' },
                          { id: 'large', label: 'Nyaman (105%)' },
                        ].map(item => (
                          <label key={item.id} className="flex items-center gap-1.5 cursor-pointer opacity-85">
                            <input
                              type="radio"
                              name="font_size_scale"
                              checked={draftTheme.fontSizeScale === item.id}
                              onChange={() => setDraftTheme({ ...draftTheme, fontSizeScale: item.id as any })}
                              style={{ accentColor: theme.colorButton }}
                            />
                            <span style={{ color: theme.colorText }}>{item.label}</span>
                          </label>
                        ))}
                      </div>
                    </div>
                  </div>

                  {/* Bagian C: Identitas Website & Logo */}
                  <div
                    className="p-5 sm:p-6 border shadow-xs"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      borderRadius: theme.cardRadius,
                    }}
                  >
                    <div className="flex items-center gap-2 mb-2">
                      <span
                        className="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        style={{ backgroundColor: theme.colorSecondary, color: theme.colorButton }}
                      >
                        <ShieldCheck className="w-4 h-4" />
                      </span>
                      <h3 className="font-bold text-sm" style={{ color: theme.colorText }}>
                        Identitas & Logo Website
                      </h3>
                    </div>

                    <div className="space-y-4 text-xs mt-3">
                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>
                            Nama Aplikasi / Kos
                          </label>
                          <input
                            type="text"
                            value={draftTheme.appName}
                            onChange={e => setDraftTheme({ ...draftTheme, appName: e.target.value })}
                            className="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none"
                            style={{
                              backgroundColor: theme.colorCard,
                              borderColor: theme.colorSecondary,
                              color: theme.colorText,
                            }}
                            placeholder="Contoh: KosCare"
                            required
                          />
                        </div>
                        <div>
                          <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>
                            Tagline Singkat
                          </label>
                          <input
                            type="text"
                            value={draftTheme.appTagline}
                            onChange={e => setDraftTheme({ ...draftTheme, appTagline: e.target.value })}
                            className="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none"
                            style={{
                              backgroundColor: theme.colorCard,
                              borderColor: theme.colorSecondary,
                              color: theme.colorText,
                            }}
                            placeholder="Contoh: Perawatan Kos Jadi Lebih Mudah"
                            required
                          />
                        </div>
                      </div>

                      {/* Logo Uploader */}
                      <div className="p-4 rounded-2xl border" style={{ backgroundColor: `${theme.colorSecondary}20`, borderColor: theme.colorSecondary }}>
                        <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>
                          Unggah Logo Website
                        </label>
                        <p className="text-[11px] opacity-75 mb-3" style={{ color: theme.colorText }}>
                          Format yang didukung: PNG, JPG, JPEG, WebP (Maksimal 2MB).
                        </p>

                        <div className="flex flex-wrap items-center gap-4">
                          <label
                            className="flex items-center gap-1.5 px-4 py-2 rounded-full border border-dashed cursor-pointer font-semibold text-xs shadow-2xs hover:opacity-90"
                            style={{
                              borderColor: theme.colorButton,
                              backgroundColor: theme.colorCard,
                              color: theme.colorText,
                            }}
                          >
                            <Upload className="w-3.5 h-3.5" style={{ color: theme.colorButton }} />
                            <span>Pilih File Logo</span>
                            <input
                              type="file"
                              accept=".png, .jpg, .jpeg, .webp"
                              className="hidden"
                              onChange={e => {
                                const file = e.target.files?.[0];
                                if (!file) return;
                                if (file.size > 2 * 1024 * 1024) {
                                  showNotification('Ukuran file logo maksimal adalah 2MB.');
                                  return;
                                }
                                const reader = new FileReader();
                                reader.onload = ev => {
                                  if (ev.target?.result) {
                                    setDraftTheme({ ...draftTheme, logoUrl: ev.target.result as string });
                                    showNotification('Logo berhasil dimuat ke pratinjau.');
                                  }
                                };
                                reader.readAsDataURL(file);
                              }}
                            />
                          </label>

                          {draftTheme.logoUrl && (
                            <button
                              type="button"
                              onClick={() => {
                                setDraftTheme({ ...draftTheme, logoUrl: null });
                                showNotification('Logo kustom dihapus dan diganti dengan ikon standar.');
                              }}
                              className="flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-semibold px-2 py-1"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                              <span>Hapus Logo</span>
                            </button>
                          )}
                        </div>

                        {/* Logo Preview & Size */}
                        <div className="mt-3 flex items-center gap-4 pt-3 border-t" style={{ borderColor: `${theme.colorSecondary}80` }}>
                          <div
                            className="w-24 h-12 rounded-xl border flex items-center justify-center p-1"
                            style={{ backgroundColor: theme.colorCard, borderColor: theme.colorSecondary }}
                          >
                            {draftTheme.logoUrl ? (
                              <img src={draftTheme.logoUrl} alt="Preview" className="max-h-full max-w-full object-contain" />
                            ) : (
                              <span style={{ color: theme.colorButton }}><House className="w-5 h-5" /></span>
                            )}
                          </div>
                          <div>
                            <span className="block font-semibold mb-1" style={{ color: theme.colorText }}>Ukuran Logo:</span>
                            <div className="flex items-center gap-3">
                              {(['small', 'medium', 'large'] as const).map(sz => (
                                <label key={sz} className="flex items-center gap-1 text-[11px] capitalize cursor-pointer">
                                  <input
                                    type="radio"
                                    name="logo_size"
                                    checked={draftTheme.logoSize === sz}
                                    onChange={() => setDraftTheme({ ...draftTheme, logoSize: sz })}
                                    style={{ accentColor: theme.colorButton }}
                                  />
                                  <span style={{ color: theme.colorText }}>
                                    {sz === 'small' ? 'Kecil' : sz === 'medium' ? 'Sedang' : 'Besar'}
                                  </span>
                                </label>
                              ))}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Bagian D: Bentuk Komponen & Kartu */}
                  <div
                    className="p-5 sm:p-6 border shadow-xs"
                    style={{
                      backgroundColor: theme.colorCard,
                      borderColor: theme.colorSecondary,
                      borderRadius: theme.cardRadius,
                    }}
                  >
                    <div className="flex items-center gap-2 mb-2">
                      <span
                        className="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                        style={{ backgroundColor: theme.colorSecondary, color: theme.colorButton }}
                      >
                        <Check className="w-4 h-4" />
                      </span>
                      <h3 className="font-bold text-sm" style={{ color: theme.colorText }}>
                        Bentuk Komponen & Kartu
                      </h3>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mt-3">
                      <div>
                        <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>
                          Radius Sudut Kartu
                        </label>
                        <select
                          value={draftTheme.cardRadius}
                          onChange={e => setDraftTheme({ ...draftTheme, cardRadius: e.target.value })}
                          className="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        >
                          <option value="0.5rem">Kotak Halus (8px)</option>
                          <option value="1rem">Lembut / Standar (16px - Bawaan)</option>
                          <option value="1.5rem">Ekstra Bulat (24px)</option>
                        </select>
                      </div>

                      <div>
                        <label className="block font-semibold mb-1" style={{ color: theme.colorText }}>
                          Bentuk Tombol
                        </label>
                        <select
                          value={draftTheme.buttonShape}
                          onChange={e => setDraftTheme({ ...draftTheme, buttonShape: e.target.value as any })}
                          className="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none"
                          style={{
                            backgroundColor: theme.colorCard,
                            borderColor: theme.colorSecondary,
                            color: theme.colorText,
                          }}
                        >
                          <option value="pill">Kapsul / Membulat Penuh (Bawaan)</option>
                          <option value="rounded">Membulat Sedang (10px)</option>
                          <option value="square">Kotak Halus (4px)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  {/* Tombol Aksi Simpan & Batal */}
                  <div className="flex flex-wrap items-center gap-3 pt-2">
                    <button
                      type="button"
                      onClick={handleSaveTheme}
                      className="px-6 py-3 text-white font-bold text-xs shadow-sm hover:opacity-90 transition flex items-center gap-2"
                      style={{
                        backgroundColor: draftTheme.colorButton,
                        borderRadius: currentBtnRadius,
                      }}
                    >
                      <Check className="w-4 h-4" />
                      <span>Simpan Perubahan</span>
                    </button>

                    <button
                      type="button"
                      onClick={handleCancelTheme}
                      className="px-5 py-3 border font-semibold text-xs transition hover:opacity-85"
                      style={{
                        borderColor: theme.colorSecondary,
                        color: theme.colorText,
                        backgroundColor: theme.colorCard,
                        borderRadius: currentBtnRadius,
                      }}
                    >
                      Batalkan
                    </button>
                  </div>
                </div>

                {/* Kolom Kanan: PRATINJAU LANGSUNG (5 Kolom) */}
                <div className="lg:col-span-5 lg:sticky lg:top-20">
                  <div
                    className="p-5 border shadow-sm transition-all duration-200"
                    style={{
                      backgroundColor: draftTheme.colorBackground,
                      borderColor: draftTheme.colorSecondary,
                      borderRadius: draftTheme.cardRadius,
                      fontFamily: draftTheme.fontFamily,
                    }}
                  >
                    <div className="flex items-center justify-between pb-3 mb-3 border-b border-black/10">
                      <div className="flex items-center gap-2">
                        <Palette className="w-4 h-4" style={{ color: draftTheme.colorButton }} />
                        <span className="font-bold text-xs" style={{ color: draftTheme.colorText }}>
                          Pratinjau Langsung Tema
                        </span>
                      </div>
                      <span
                        className="px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                        style={{
                          backgroundColor: draftTheme.colorSecondary,
                          color: draftTheme.colorText,
                          borderColor: `${draftTheme.colorPrimary}40`,
                        }}
                      >
                        {draftTheme.presetName}
                      </span>
                    </div>

                    {/* Simulasi Navbar */}
                    <div
                      className="p-2.5 rounded-2xl border mb-3 flex items-center justify-between shadow-2xs"
                      style={{
                        backgroundColor: draftTheme.colorCard,
                        borderColor: `${draftTheme.colorSecondary}80`,
                      }}
                    >
                      <div className="flex items-center gap-2">
                        {draftTheme.logoUrl ? (
                          <img
                            src={draftTheme.logoUrl}
                            alt="Logo"
                            style={{
                              height: draftTheme.logoSize === 'small' ? '22px' : draftTheme.logoSize === 'large' ? '32px' : '26px',
                              maxWidth: '80px',
                              objectFit: 'contain',
                            }}
                          />
                        ) : (
                          <span
                            className="w-6 h-6 rounded-full flex items-center justify-center text-xs"
                            style={{ backgroundColor: draftTheme.colorSecondary, color: draftTheme.colorButton }}
                          >
                            <House className="w-3.5 h-3.5" />
                          </span>
                        )}
                        <span className="font-bold text-xs" style={{ color: draftTheme.colorText }}>
                          {draftTheme.appName}
                        </span>
                      </div>
                      <span
                        className="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                        style={{ backgroundColor: draftTheme.colorSecondary, color: draftTheme.colorText }}
                      >
                        Pengelola
                      </span>
                    </div>

                    {/* Simulasi Kartu Laporan Contoh */}
                    <div
                      className="p-4 mb-3 border shadow-2xs transition-all"
                      style={{
                        backgroundColor: draftTheme.colorCard,
                        borderColor: `${draftTheme.colorSecondary}80`,
                        borderRadius: draftTheme.cardRadius,
                      }}
                    >
                      <div className="flex items-center justify-between mb-2">
                        <span
                          className="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                          style={{ backgroundColor: draftTheme.colorSecondary, color: draftTheme.colorText }}
                        >
                          Tinggi
                        </span>
                        <span className="text-[10px] opacity-60">KC-202610-0001</span>
                      </div>

                      <h4 className="font-bold text-xs mb-1" style={{ color: draftTheme.colorText }}>
                        Kran Air Kamar Mandi Bocor
                      </h4>
                      <p className="text-[11px] mb-3 opacity-80" style={{ color: draftTheme.colorText }}>
                        Kamar 102 &bull; Pelapor: Dimas Anggoro
                      </p>

                      <div className="flex items-center gap-2">
                        <button
                          type="button"
                          className="px-3 py-1.5 text-white font-bold text-[11px] shadow-xs hover:opacity-90"
                          style={{
                            backgroundColor: draftTheme.colorButton,
                            borderRadius: draftTheme.buttonShape === 'pill' ? '9999px' : draftTheme.buttonShape === 'rounded' ? '8px' : '3px',
                          }}
                        >
                          Periksa Laporan
                        </button>
                        <button
                          type="button"
                          className="px-3 py-1.5 border text-[11px] font-semibold hover:opacity-85"
                          style={{
                            borderColor: draftTheme.colorSecondary,
                            color: draftTheme.colorText,
                            borderRadius: draftTheme.buttonShape === 'pill' ? '9999px' : draftTheme.buttonShape === 'rounded' ? '8px' : '3px',
                          }}
                        >
                          Detail
                        </button>
                      </div>
                    </div>

                    {/* Simulasi Tagline */}
                    <p className="text-center text-[11px] italic mb-0 opacity-80" style={{ color: draftTheme.colorText }}>
                      “{draftTheme.appTagline}”
                    </p>

                    <p className="text-[10px] text-center opacity-60 mt-3 pt-2 border-t border-black/5">
                      Pratinjau ini bereaksi seketika saat Anda memilih tema atau font sebelum disimpan.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          )}
        </main>
      )}

      {/* Footer Minimalis */}
      <footer
        className="border-t py-6 mt-auto text-center text-xs transition-colors"
        style={{
          backgroundColor: `${theme.colorCard}F0`,
          borderColor: theme.colorSecondary,
          color: theme.colorText,
        }}
      >
        <div className="max-w-6xl mx-auto px-4">
          <p className="mb-0 opacity-80">
            &copy; {new Date().getFullYear()} {theme.appName}. {theme.appTagline}. Seluruh hak cipta dilindungi.
          </p>
        </div>
      </footer>
    </div>
  );
}
