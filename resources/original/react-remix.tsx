import { useState } from 'react';
import { ChevronRight, ChevronLeft, Check, Plane, User, MapPin, DollarSign, Send, Clock, CheckCircle, XCircle, LogOut, Mail, FileText, Receipt, Eye, X, Users, Shield, Download } from 'lucide-react';

const users = [
  { id: 1, name: 'John Doe', email: 'john@company.com', role: 'employee', department: 'Engineering', jobGrade: 'L3', supervisor: 'Jane Smith' },
  { id: 2, name: 'Jane Smith', email: 'jane@company.com', role: 'supervisor', department: 'Engineering', jobGrade: 'L5' },
  { id: 3, name: 'Robert Wilson', email: 'robert@company.com', role: 'md', department: 'Executive', jobGrade: 'L8' }
];

const initForm = {
  name: '', department: '', jobGrade: '', immediateSuperior: '', tarId: '', project: '', projectOwner: '', travelDescription: '',
  routes: [{ destination: '', modeOfTravel: '', departure: '', arrival: '' }],
  noOfDays: '', country: '', location: '', hotelName: '', quotedRate: '', justification: '',
  accommodationPaidBy: 'Company', mealsPaidBy: 'Company', accommodationEligibility: '', mealsAllowanceEligibility: '',
  accommodation: '', mealAllowance: '', groundTransport: '', airFare: '', others: '', advanceAmount: ''
};

const steps = ['Applicant', 'TAR Details', 'Routes', 'Travel Summary', 'Costs', 'Review'];

export default function TARApp() {
  const [user, setUser] = useState(null);
  const [view, setView] = useState('login');
  const [step, setStep] = useState(0);
  const [form, setForm] = useState(initForm);
  const [notifs, setNotifs] = useState([]);
  const [showNotif, setShowNotif] = useState(false);
  const [modal, setModal] = useState(null);
  const [expForm, setExpForm] = useState({ accommodation: '', meals: '', transport: '', airfare: '', others: '' });
  const [requests, setRequests] = useState([
    { id: 'TAR-001234', oderId: 1, name: 'John Doe', email: 'john@company.com', project: 'Project Alpha', destination: 'Singapore', country: 'Singapore', location: 'Marina Bay', hotelName: 'Marina Bay Sands', noOfDays: '3D/2N', status: 'pending_supervisor', date: '2024-01-15', total: 15000000, advance: 10000000, form: initForm, approvals: [], expenses: null },
    { id: 'TAR-001235', oderId: 1, name: 'John Doe', email: 'john@company.com', project: 'Project Beta', destination: 'Jakarta', country: 'Indonesia', location: 'Jakarta', hotelName: 'Grand Hyatt', noOfDays: '2D/1N', status: 'approved', date: '2024-01-14', total: 8500000, advance: 6000000, form: initForm, approvals: [{ role: 'Supervisor', name: 'Jane Smith', date: '2024-01-14', status: 'approved' }, { role: 'MD', name: 'Robert Wilson', date: '2024-01-15', status: 'approved' }], expenses: null }
  ]);

  const login = u => { setUser(u); setView('dashboard'); setNotifs([{ id: 1, msg: 'Welcome to TAR System', read: false }]); };
  const logout = () => { setUser(null); setView('login'); };
  const upForm = (k, v) => setForm(p => ({ ...p, [k]: v }));
  const upRoute = (i, k, v) => { const r = [...form.routes]; r[i] = { ...r[i], [k]: v }; setForm(p => ({ ...p, routes: r })); };
  const addRoute = () => setForm(p => ({ ...p, routes: [...p.routes, { destination: '', modeOfTravel: '', departure: '', arrival: '' }] }));
  const total = [form.accommodation, form.mealAllowance, form.groundTransport, form.airFare, form.others].reduce((s, v) => s + (parseFloat(v) || 0), 0);
  const fmt = n => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n || 0);
  const statusLabel = s => ({ pending_supervisor: 'Pending Supervisor', pending_md: 'Pending MD', approved: 'Approved', rejected: 'Rejected', completed: 'Completed' }[s] || s);
  
  const visibleReqs = () => {
    if (!user) return [];
    if (user.role === 'employee') return requests.filter(r => r.oderId === user.id);
    if (user.role === 'supervisor') return requests.filter(r => r.status === 'pending_supervisor' || r.oderId === user.id);
    return requests;
  };

  const submit = () => {
    const req = { id: form.tarId, oderId: user.id, name: form.name, email: user.email, project: form.project, destination: form.routes[0]?.destination || '', country: form.country, location: form.location, hotelName: form.hotelName, noOfDays: form.noOfDays, status: 'pending_supervisor', date: new Date().toISOString().split('T')[0], total, advance: parseFloat(form.advanceAmount) || 0, form: { ...form }, approvals: [], expenses: null };
    setRequests(p => [req, ...p]);
    setNotifs(p => [...p, { id: Date.now(), msg: `${form.tarId} submitted. Email sent to supervisor.`, read: false }]);
    setForm({ ...initForm, tarId: `TAR-${Date.now().toString().slice(-6)}`, name: user.name, department: user.department, jobGrade: user.jobGrade, immediateSuperior: user.supervisor || '' });
    setStep(0); setView('dashboard');
  };

  const approve = (id, ok) => {
    setRequests(p => p.map(r => {
      if (r.id !== id) return r;
      const a = { role: user.role === 'supervisor' ? 'Supervisor' : 'MD', name: user.name, date: new Date().toISOString().split('T')[0], status: ok ? 'approved' : 'rejected' };
      const ns = ok ? (r.status === 'pending_supervisor' ? 'pending_md' : 'approved') : 'rejected';
      return { ...r, status: ns, approvals: [...r.approvals, a] };
    }));
    setNotifs(p => [...p, { id: Date.now(), msg: `${id} ${ok ? 'approved' : 'rejected'}. Email sent.`, read: false }]);
    setModal(null);
  };

  const submitExp = id => {
    const t = [expForm.accommodation, expForm.meals, expForm.transport, expForm.airfare, expForm.others].reduce((s, v) => s + (parseFloat(v) || 0), 0);
    setRequests(p => p.map(r => r.id === id ? { ...r, status: 'completed', expenses: { ...expForm, total: t } } : r));
    setNotifs(p => [...p, { id: Date.now(), msg: `Expenses for ${id} submitted.`, read: false }]);
    setExpForm({ accommodation: '', meals: '', transport: '', airfare: '', others: '' });
    setModal(null);
  };

  const genPDF = r => {
    const txt = `TRAVEL AUTHORIZATION REQUEST\n${'='.repeat(30)}\nTAR ID: ${r.id}\nDate: ${r.date}\nStatus: ${statusLabel(r.status)}\n\nAPPLICANT: ${r.name} (${r.email})\nPROJECT: ${r.project}\n\nDESTINATION: ${r.location}, ${r.country}\nDURATION: ${r.noOfDays}\nHOTEL: ${r.hotelName}\n\nESTIMATED: ${fmt(r.total)}\nADVANCE: ${fmt(r.advance)}\n\nAPPROVALS:\n${r.approvals.map(a => `- ${a.role}: ${a.name} (${a.status}) ${a.date}`).join('\n') || 'Pending'}\n${r.expenses ? `\nEXPENSES:\nTotal: ${fmt(r.expenses.total)}\nSettlement: ${r.expenses.total > r.advance ? 'Reimburse ' + fmt(r.expenses.total - r.advance) : 'Return ' + fmt(r.advance - r.expenses.total)}` : ''}\n\n${'='.repeat(30)}\nBRI Tower, Jl. Asia Afrika 57, Bandung`;
    const b = new Blob([txt], { type: 'text/plain' });
    const a = document.createElement('a'); a.href = URL.createObjectURL(b); a.download = `${r.id}.txt`; a.click();
  };

  const Input = ({ label, value, onChange, type = 'text', ...p }) => (
    <div className="mb-3">
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <input type={type} value={value} onChange={e => onChange(e.target.value)} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" {...p} />
    </div>
  );

  const Sel = ({ label, value, onChange, opts }) => (
    <div className="mb-3">
      <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
      <select value={value} onChange={e => onChange(e.target.value)} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
        {opts.map(o => <option key={o} value={o}>{o}</option>)}
      </select>
    </div>
  );

  const Badge = ({ status }) => {
    const c = { pending_supervisor: 'bg-yellow-100 text-yellow-800', pending_md: 'bg-orange-100 text-orange-800', approved: 'bg-green-100 text-green-800', rejected: 'bg-red-100 text-red-800', completed: 'bg-blue-100 text-blue-800' };
    return <span className={`px-2 py-1 rounded-full text-xs font-medium ${c[status]}`}>{statusLabel(status)}</span>;
  };

  const Modal = ({ title, children, onClose }) => (
    <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-auto" onClick={e => e.stopPropagation()}>
        <div className="flex items-center justify-between p-4 border-b sticky top-0 bg-white">
          <h3 className="font-semibold">{title}</h3>
          <button onClick={onClose} className="p-1 hover:bg-gray-100 rounded"><X size={20} /></button>
        </div>
        <div className="p-4">{children}</div>
      </div>
    </div>
  );

  if (view === 'login') return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 flex items-center justify-center p-4">
      <div className="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4"><Plane className="text-white" size={32} /></div>
          <h1 className="text-2xl font-bold">TAR System</h1>
          <p className="text-gray-500 text-sm">Travel Authorization Request</p>
        </div>
        <p className="text-sm text-gray-600 mb-4 text-center">Select user to login:</p>
        <div className="space-y-3">
          {users.map(u => (
            <button key={u.id} onClick={() => login(u)} className="w-full p-4 border-2 rounded-xl hover:border-blue-500 hover:bg-blue-50 text-left flex items-center gap-4">
              <div className={`w-10 h-10 rounded-full flex items-center justify-center ${u.role === 'md' ? 'bg-purple-100' : u.role === 'supervisor' ? 'bg-green-100' : 'bg-blue-100'}`}>
                {u.role === 'md' ? <Shield size={20} className="text-purple-600" /> : u.role === 'supervisor' ? <Users size={20} className="text-green-600" /> : <User size={20} className="text-blue-600" />}
              </div>
              <div>
                <p className="font-medium">{u.name}</p>
                <p className="text-xs text-gray-500 capitalize">{u.role} • {u.department}</p>
              </div>
            </button>
          ))}
        </div>
      </div>
    </div>
  );

  if (view === 'form') {
    const StepC = () => {
      if (step === 0) return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input label="Name *" value={form.name} onChange={v => upForm('name', v)} />
          <Input label="Department *" value={form.department} onChange={v => upForm('department', v)} />
          <Input label="Job Grade" value={form.jobGrade} onChange={v => upForm('jobGrade', v)} />
          <Input label="Immediate Superior *" value={form.immediateSuperior} onChange={v => upForm('immediateSuperior', v)} />
        </div>
      );
      if (step === 1) return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input label="TAR ID" value={form.tarId} onChange={() => {}} disabled />
          <Input label="Project *" value={form.project} onChange={v => upForm('project', v)} />
          <Input label="Project Owner" value={form.projectOwner} onChange={v => upForm('projectOwner', v)} />
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Travel Description *</label>
            <textarea value={form.travelDescription} onChange={e => upForm('travelDescription', e.target.value)} rows={3} className="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" />
          </div>
        </div>
      );
      if (step === 2) return (
        <div>
          {form.routes.map((r, i) => (
            <div key={i} className="p-4 bg-gray-50 rounded-lg mb-4">
              <p className="font-medium mb-3 text-sm">Route {i + 1}</p>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                <Input label="Destination *" value={r.destination} onChange={v => upRoute(i, 'destination', v)} />
                <Sel label="Mode" value={r.modeOfTravel} onChange={v => upRoute(i, 'modeOfTravel', v)} opts={['', 'Flight', 'Train', 'Bus', 'Car']} />
                <Input label="Departure" type="datetime-local" value={r.departure} onChange={v => upRoute(i, 'departure', v)} />
                <Input label="Arrival" type="datetime-local" value={r.arrival} onChange={v => upRoute(i, 'arrival', v)} />
              </div>
            </div>
          ))}
          <button onClick={addRoute} className="text-blue-600 text-sm font-medium">+ Add Route</button>
        </div>
      );
      if (step === 3) return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input label="Days/Nights *" value={form.noOfDays} onChange={v => upForm('noOfDays', v)} />
          <Input label="Country *" value={form.country} onChange={v => upForm('country', v)} />
          <Input label="Location *" value={form.location} onChange={v => upForm('location', v)} />
          <Input label="Hotel" value={form.hotelName} onChange={v => upForm('hotelName', v)} />
          <Input label="Quoted Rate (IDR)" type="number" value={form.quotedRate} onChange={v => upForm('quotedRate', v)} />
          <Sel label="Accommodation By" value={form.accommodationPaidBy} onChange={v => upForm('accommodationPaidBy', v)} opts={['Company', 'Self', 'Client']} />
          <Sel label="Meals By" value={form.mealsPaidBy} onChange={v => upForm('mealsPaidBy', v)} opts={['Company', 'Self', 'Client']} />
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Justification</label>
            <textarea value={form.justification} onChange={e => upForm('justification', e.target.value)} rows={2} className="w-full px-3 py-2 border rounded-lg text-sm" />
          </div>
        </div>
      );
      if (step === 4) return (
        <div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <Input label="Accommodation (IDR)" type="number" value={form.accommodation} onChange={v => upForm('accommodation', v)} />
            <Input label="Meal Allowance (IDR)" type="number" value={form.mealAllowance} onChange={v => upForm('mealAllowance', v)} />
            <Input label="Transport (IDR)" type="number" value={form.groundTransport} onChange={v => upForm('groundTransport', v)} />
            <Input label="Air Fare (IDR)" type="number" value={form.airFare} onChange={v => upForm('airFare', v)} />
            <Input label="Others (IDR)" type="number" value={form.others} onChange={v => upForm('others', v)} />
          </div>
          <div className="bg-blue-50 p-4 rounded-lg mb-4 flex justify-between font-semibold">
            <span>Total:</span><span className="text-blue-600">{fmt(total)}</span>
          </div>
          <div className="bg-gray-50 p-4 rounded-lg">
            <p className="text-sm font-medium mb-1">Advance Amount</p>
            <p className="text-xs text-gray-500 mb-2">90% Meal (Local), 100% Meal+Hotel (Overseas)</p>
            <Input label="Amount (IDR)" type="number" value={form.advanceAmount} onChange={v => upForm('advanceAmount', v)} />
          </div>
        </div>
      );
      return (
        <div className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="bg-gray-50 p-4 rounded-lg">
              <p className="font-semibold text-sm mb-2">Applicant</p>
              <p className="text-sm">{form.name}</p>
              <p className="text-sm text-gray-500">{form.department} • {form.jobGrade}</p>
            </div>
            <div className="bg-gray-50 p-4 rounded-lg">
              <p className="font-semibold text-sm mb-2">Destination</p>
              <p className="text-sm">{form.location}, {form.country}</p>
              <p className="text-sm text-gray-500">{form.noOfDays} • {form.hotelName}</p>
            </div>
            <div className="bg-blue-50 p-4 rounded-lg md:col-span-2">
              <p className="font-semibold text-sm mb-2">Cost Summary</p>
              <div className="flex justify-between text-sm"><span>Total:</span><span className="font-medium">{fmt(total)}</span></div>
              <div className="flex justify-between text-sm"><span>Advance:</span><span className="font-medium">{fmt(parseFloat(form.advanceAmount) || 0)}</span></div>
            </div>
          </div>
          <div className="bg-yellow-50 border border-yellow-200 p-3 rounded-lg flex gap-2 text-sm text-yellow-800">
            <Mail size={16} className="shrink-0 mt-0.5" />Email will be sent to supervisor upon submission.
          </div>
        </div>
      );
    };

    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 p-4">
        <div className="max-w-3xl mx-auto">
          <button onClick={() => setView('dashboard')} className="text-slate-400 hover:text-white mb-4 flex items-center gap-1 text-sm"><ChevronLeft size={18} />Back</button>
          <div className="bg-white rounded-xl shadow-xl overflow-hidden">
            <div className="bg-gradient-to-r from-blue-600 to-blue-800 p-4 text-white">
              <h1 className="text-lg font-bold flex items-center gap-2"><Plane size={20} />New Travel Request</h1>
            </div>
            <div className="p-3 border-b bg-gray-50 overflow-x-auto">
              <div className="flex gap-2 min-w-max">
                {steps.map((s, i) => (
                  <div key={s} className={`flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium ${i === step ? 'bg-blue-600 text-white' : i < step ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-500'}`}>
                    {i < step ? <Check size={14} /> : <span className="w-4 h-4 rounded-full bg-current/20 flex items-center justify-center">{i + 1}</span>}
                    <span className="hidden sm:inline">{s}</span>
                  </div>
                ))}
              </div>
            </div>
            <div className="p-6"><StepC /></div>
            <div className="p-4 border-t bg-gray-50 flex justify-between">
              <button onClick={() => setStep(p => p - 1)} disabled={step === 0} className="px-4 py-2 rounded-lg font-medium flex items-center gap-1 disabled:opacity-50 hover:bg-gray-200 text-sm"><ChevronLeft size={16} />Prev</button>
              {step === 5 ? (
                <button onClick={submit} className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2 text-sm"><Send size={16} />Submit</button>
              ) : (
                <button onClick={() => setStep(p => p + 1)} className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-1 text-sm">Next<ChevronRight size={16} /></button>
              )}
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 p-4">
      <div className="max-w-6xl mx-auto">
        <div className="flex items-center justify-between mb-6 flex-wrap gap-4">
          <div>
            <h1 className="text-xl font-bold text-white flex items-center gap-2"><Plane className="text-blue-400" />TAR System</h1>
            <p className="text-slate-400 text-sm">Welcome, {user.name} <span className="capitalize">({user.role})</span></p>
          </div>
          <div className="flex items-center gap-2">
            <div className="relative">
              <button onClick={() => setShowNotif(!showNotif)} className="relative p-2 bg-white/10 rounded-lg hover:bg-white/20">
                <Mail size={18} className="text-white" />
                {notifs.filter(n => !n.read).length > 0 && <span className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{notifs.filter(n => !n.read).length}</span>}
              </button>
              {showNotif && (
                <div className="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl z-50">
                  <div className="p-2 border-b font-medium text-sm">Notifications</div>
                  <div className="max-h-48 overflow-auto">
                    {notifs.length === 0 ? <p className="p-2 text-sm text-gray-500">No notifications</p> : notifs.map(n => (
                      <div key={n.id} className={`p-2 border-b text-xs cursor-pointer ${n.read ? '' : 'bg-blue-50'}`} onClick={() => setNotifs(p => p.map(x => x.id === n.id ? { ...x, read: true } : x))}>{n.msg}</div>
                    ))}
                  </div>
                </div>
              )}
            </div>
            {user.role === 'employee' && <button onClick={() => { setForm({ ...initForm, tarId: `TAR-${Date.now().toString().slice(-6)}`, name: user.name, department: user.department, jobGrade: user.jobGrade, immediateSuperior: user.supervisor || '' }); setView('form'); }} className="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-1"><Send size={14} />New TAR</button>}
            <button onClick={logout} className="p-2 bg-white/10 rounded-lg hover:bg-white/20"><LogOut size={18} className="text-white" /></button>
          </div>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
          {[['Pending', visibleReqs().filter(r => r.status.startsWith('pending')).length, 'bg-yellow-500'], ['Approved', visibleReqs().filter(r => r.status === 'approved').length, 'bg-green-500'], ['Rejected', visibleReqs().filter(r => r.status === 'rejected').length, 'bg-red-500'], ['Completed', visibleReqs().filter(r => r.status === 'completed').length, 'bg-blue-500']].map(([l, c, bg]) => (
            <div key={l} className="bg-white/10 rounded-xl p-3">
              <div className="flex items-center justify-between">
                <span className="text-slate-300 text-sm">{l}</span>
                <span className={`${bg} text-white font-bold px-2 py-0.5 rounded text-sm`}>{c}</span>
              </div>
            </div>
          ))}
        </div>

        <div className="bg-white rounded-xl shadow-xl overflow-hidden">
          <div className="p-4 border-b bg-gray-50 font-semibold">Travel Requests</div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50 text-left text-gray-600">
                <tr><th className="px-4 py-3">ID</th><th className="px-4 py-3">Applicant</th><th className="px-4 py-3">Destination</th><th className="px-4 py-3">Amount</th><th className="px-4 py-3">Status</th><th className="px-4 py-3">Actions</th></tr>
              </thead>
              <tbody className="divide-y">
                {visibleReqs().map(r => (
                  <tr key={r.id} className="hover:bg-gray-50">
                    <td className="px-4 py-3 font-medium text-blue-600">{r.id}</td>
                    <td className="px-4 py-3">{r.name}</td>
                    <td className="px-4 py-3">{r.destination}</td>
                    <td className="px-4 py-3">{fmt(r.total)}</td>
                    <td className="px-4 py-3"><Badge status={r.status} /></td>
                    <td className="px-4 py-3">
                      <div className="flex gap-1">
                        <button onClick={() => setModal({ type: 'view', data: r })} className="p-1.5 hover:bg-gray-100 rounded" title="View"><Eye size={16} /></button>
                        <button onClick={() => genPDF(r)} className="p-1.5 hover:bg-gray-100 rounded" title="Download"><Download size={16} /></button>
                        {((user.role === 'supervisor' && r.status === 'pending_supervisor') || (user.role === 'md' && r.status === 'pending_md')) && (
                          <>
                            <button onClick={() => approve(r.id, true)} className="p-1.5 hover:bg-green-100 rounded text-green-600" title="Approve"><CheckCircle size={16} /></button>
                            <button onClick={() => approve(r.id, false)} className="p-1.5 hover:bg-red-100 rounded text-red-600" title="Reject"><XCircle size={16} /></button>
                          </>
                        )}
                        {user.role === 'employee' && r.status === 'approved' && !r.expenses && (
                          <button onClick={() => setModal({ type: 'expense', data: r })} className="p-1.5 hover:bg-blue-100 rounded text-blue-600" title="Submit Expenses"><Receipt size={16} /></button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
            {visibleReqs().length === 0 && <p className="p-8 text-center text-gray-500">No requests found</p>}
          </div>
        </div>

        {modal?.type === 'view' && (
          <Modal title={`TAR Details - ${modal.data.id}`} onClose={() => setModal(null)}>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><p className="text-xs text-gray-500">Applicant</p><p className="font-medium">{modal.data.name}</p></div>
                <div><p className="text-xs text-gray-500">Email</p><p className="font-medium">{modal.data.email}</p></div>
                <div><p className="text-xs text-gray-500">Project</p><p className="font-medium">{modal.data.project}</p></div>
                <div><p className="text-xs text-gray-500">Date</p><p className="font-medium">{modal.data.date}</p></div>
                <div><p className="text-xs text-gray-500">Destination</p><p className="font-medium">{modal.data.location}, {modal.data.country}</p></div>
                <div><p className="text-xs text-gray-500">Duration</p><p className="font-medium">{modal.data.noOfDays}</p></div>
                <div><p className="text-xs text-gray-500">Hotel</p><p className="font-medium">{modal.data.hotelName}</p></div>
                <div><p className="text-xs text-gray-500">Status</p><Badge status={modal.data.status} /></div>
              </div>
              <div className="border-t pt-4">
                <p className="font-semibold mb-2">Cost Summary</p>
                <div className="bg-gray-50 p-3 rounded-lg grid grid-cols-2 gap-2 text-sm">
                  <div>Estimated Total:</div><div className="text-right font-medium">{fmt(modal.data.total)}</div>
                  <div>Advance Amount:</div><div className="text-right font-medium">{fmt(modal.data.advance)}</div>
                </div>
              </div>
              <div className="border-t pt-4">
                <p className="font-semibold mb-2">Approval History</p>
                {modal.data.approvals.length === 0 ? <p className="text-sm text-gray-500">Pending approval</p> : (
                  <div className="space-y-2">
                    {modal.data.approvals.map((a, i) => (
                      <div key={i} className={`p-2 rounded-lg text-sm flex justify-between ${a.status === 'approved' ? 'bg-green-50' : 'bg-red-50'}`}>
                        <span><strong>{a.role}:</strong> {a.name}</span>
                        <span className={a.status === 'approved' ? 'text-green-600' : 'text-red-600'}>{a.status.toUpperCase()} ({a.date})</span>
                      </div>
                    ))}
                  </div>
                )}
              </div>
              {modal.data.expenses && (
                <div className="border-t pt-4">
                  <p className="font-semibold mb-2">Actual Expenses</p>
                  <div className="bg-blue-50 p-3 rounded-lg text-sm space-y-1">
                    <div className="flex justify-between"><span>Accommodation:</span><span>{fmt(modal.data.expenses.accommodation)}</span></div>
                    <div className="flex justify-between"><span>Meals:</span><span>{fmt(modal.data.expenses.meals)}</span></div>
                    <div className="flex justify-between"><span>Transport:</span><span>{fmt(modal.data.expenses.transport)}</span></div>
                    <div className="flex justify-between"><span>Airfare:</span><span>{fmt(modal.data.expenses.airfare)}</span></div>
                    <div className="flex justify-between"><span>Others:</span><span>{fmt(modal.data.expenses.others)}</span></div>
                    <div className="flex justify-between font-bold border-t pt-1 mt-1"><span>Total:</span><span>{fmt(modal.data.expenses.total)}</span></div>
                    <div className={`flex justify-between font-medium pt-1 ${modal.data.expenses.total > modal.data.advance ? 'text-green-600' : 'text-orange-600'}`}>
                      <span>Settlement:</span>
                      <span>{modal.data.expenses.total > modal.data.advance ? `Reimburse ${fmt(modal.data.expenses.total - modal.data.advance)}` : `Return ${fmt(modal.data.advance - modal.data.expenses.total)}`}</span>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </Modal>
        )}

        {modal?.type === 'expense' && (
          <Modal title={`Submit Expenses - ${modal.data.id}`} onClose={() => setModal(null)}>
            <div className="space-y-4">
              <div className="bg-gray-50 p-3 rounded-lg text-sm">
                <div className="flex justify-between"><span>Advance Received:</span><span className="font-medium">{fmt(modal.data.advance)}</span></div>
              </div>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                <Input label="Accommodation (IDR)" type="number" value={expForm.accommodation} onChange={v => setExpForm(p => ({ ...p, accommodation: v }))} />
                <Input label="Meals (IDR)" type="number" value={expForm.meals} onChange={v => setExpForm(p => ({ ...p, meals: v }))} />
                <Input label="Transport (IDR)" type="number" value={expForm.transport} onChange={v => setExpForm(p => ({ ...p, transport: v }))} />
                <Input label="Airfare (IDR)" type="number" value={expForm.airfare} onChange={v => setExpForm(p => ({ ...p, airfare: v }))} />
                <Input label="Others (IDR)" type="number" value={expForm.others} onChange={v => setExpForm(p => ({ ...p, others: v }))} />
              </div>
              <div className="bg-blue-50 p-3 rounded-lg">
                <div className="flex justify-between font-semibold">
                  <span>Total Expenses:</span>
                  <span>{fmt([expForm.accommodation, expForm.meals, expForm.transport, expForm.airfare, expForm.others].reduce((s, v) => s + (parseFloat(v) || 0), 0))}</span>
                </div>
              </div>
              <button onClick={() => submitExp(modal.data.id)} className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">Submit Expenses</button>
            </div>
          </Modal>
        )}
      </div>
    </div>
  );
}